"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import axiosInstance from "../../../../../lib/axios"; // Relative import fix (5 levels up)
import { toast } from "react-hot-toast";
import { ArrowLeft, ChevronLeft, Save, Loader2, Shield, Store, ScanBarcode } from "lucide-react";
import { Select } from "@/components/ui/Select";

export default function CreateUser() {
    const router = useRouter();
    const [loading, setLoading] = useState(false);
    const [formData, setFormData] = useState({
        name: "",
        email: "",
        password: "",
        role: "scanner",
        is_active: true
    });

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        try {
            await axiosInstance.post("/admin/users", formData);
            toast.success("User created successfully");
            router.push("/admin/users");
        } catch (error: any) {
            toast.error(error.response?.data?.error || "Failed to create user");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="max-w-2xl mx-auto">
            <div className="flex items-center gap-4 mb-8">
                <Link href="/admin/users" className="p-2 hover:bg-gray-100 rounded-lg text-gray-500">
                    <ChevronLeft className="w-5 h-5" />
                </Link>
                <div>
                    <h2 className="text-2xl font-bold text-gray-900">Create User</h2>
                    <p className="text-gray-500 text-sm">Add a new admin, reseller, or scanner.</p>
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
                    <label className="block text-sm font-bold text-gray-700 mb-2">Password</label>
                    <input type="text" required className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all bg-white" value={formData.password} onChange={e => setFormData({ ...formData, password: e.target.value })} />
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
                        Create User
                    </button>
                </div>
            </form>
        </div>
    );
}
