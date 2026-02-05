"use client";

import { useEffect, useState, Suspense } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import Link from "next/link";
import axiosInstance from "../../../../../lib/axios"; // Relative import fix (5 levels up)
import { toast } from "react-hot-toast";
import { ArrowLeft, ChevronLeft, Save, Loader2, Shield, Store, ScanBarcode } from "lucide-react";
import { Select } from "@/components/ui/Select";

function EditUserForm() {
    const router = useRouter();
    const searchParams = useSearchParams();
    const id = searchParams.get("id");
    const [loading, setLoading] = useState(false);
    const [fetching, setFetching] = useState(true);
    const [formData, setFormData] = useState({
        name: "",
        email: "",
        password: "", // Optional update
        role: "",
        is_active: true
    });

    useEffect(() => {
        if (!id) return;
        const fetchData = async () => {
            try {
                // Assuming we can fetch single user, but backend ListUsers returns all.
                // We'll fetch all and find by ID for now, or assume backend supports ?id= filtering on list
                const res = await axiosInstance.get("/admin/users");
                const found = res.data.find((u: any) => u.id === Number(id));
                if (found) {
                    setFormData({
                        name: found.name,
                        email: found.email,
                        password: "", // Don't prefill
                        role: found.role,
                        is_active: found.is_active
                    });
                } else {
                    toast.error("User not found");
                    router.push("/admin/users");
                }
            } catch (e) { toast.error("Failed to load user"); }
            finally { setFetching(false); }
        };
        fetchData();
    }, [id, router]);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        try {
            await axiosInstance.put(`/admin/users?id=${id}`, formData);
            toast.success("User updated");
            router.push("/admin/users");
        } catch (error: any) {
            toast.error("Failed to update user");
        } finally {
            setLoading(false);
        }
    };

    if (fetching) return <div className="flex center h-64"><Loader2 className="animate-spin text-blue-600" /></div>;

    return (
        <div className="max-w-2xl mx-auto">
            <div className="flex items-center gap-4 mb-8">
                <Link href="/admin/users" className="p-2 hover:bg-gray-100 rounded-lg text-gray-500">
                    <ChevronLeft className="w-5 h-5" />
                </Link>
                <div>
                    <h2 className="text-2xl font-bold text-gray-900">Edit User</h2>
                    <p className="text-gray-500 text-sm">Update user details.</p>
                </div>
            </div>

            <form onSubmit={handleSubmit} className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                <div>
                    <label className="block text-sm font-bold text-gray-700 mb-2">Full Name</label>
                    <input type="text" required className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all bg-white" value={formData.name} onChange={e => setFormData({ ...formData, name: e.target.value })} />
                </div>
                <div>
                    <label className="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                    <input type="email" required className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all bg-white" value={formData.email} onChange={e => setFormData({ ...formData, email: e.target.value })} />
                </div>
                <div>
                    <label className="block text-sm font-bold text-gray-700 mb-2">Password (Leave blank to keep current)</label>
                    <input type="text" className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all bg-white" placeholder="New Password" value={formData.password} onChange={e => setFormData({ ...formData, password: e.target.value })} />
                </div>
                <div className="grid grid-cols-2 gap-6">
                    <div>
                        <Select
                            label="Role"
                            value={formData.role}
                            onChange={(val) => setFormData({ ...formData, role: val })}
                            options={[
                                { label: "Admin", value: "admin" },
                                { label: "Scanner", value: "scanner" },
                                { label: "Reseller", value: "reseller" }
                            ]}
                        />
                    </div>
                    <div>
                        <Select
                            label="Status"
                            value={formData.is_active ? "true" : "false"}
                            onChange={(val) => setFormData({ ...formData, is_active: val === "true" })}
                            options={[
                                { label: "Active", value: "true" },
                                { label: "Inactive", value: "false" }
                            ]}
                        />
                    </div>
                </div>

                <div className="flex justify-end pt-4">
                    <button type="submit" disabled={loading} className="px-4 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-sm shadow-blue-600/20 disabled:opacity-70 flex items-center justify-center">
                        {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : <Save className="w-5 h-5" />}
                        Save Changes
                    </button>
                </div>
            </form>
        </div>
    );
}

export default function EditUser() {
    return (
        <Suspense fallback={<div>Loading...</div>}>
            <EditUserForm />
        </Suspense>
    );
}
