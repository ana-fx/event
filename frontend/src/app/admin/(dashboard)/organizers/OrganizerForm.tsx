"use client";

import { useState, useEffect } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import Image from "next/image";
import axiosInstance from "@/lib/axios";
import { toast } from "react-hot-toast";
import { ArrowLeft, Save, Upload, Loader2, Store } from "lucide-react";
import Link from "next/link";
import { Input } from "@/components/ui/Input";
import { Textarea } from "@/components/ui/Textarea";

interface Props {
    id?: string;
}

export default function OrganizerForm({ id }: Props) {
    const router = useRouter();
    const isEdit = !!id;

    const [loading, setLoading] = useState(false);
    const [fetching, setFetching] = useState(isEdit);
    const [preview, setPreview] = useState<string | null>(null);
    const [logoFile, setLogoFile] = useState<File | null>(null);

    const [form, setForm] = useState({
        name: "",
        username: "",
        organizer_name: "",
        email: "",
        password: "",
        phone: "",
        about_us: "",
        province: "",
        city: "",
        zip_code: "",
        address: "",
        is_active: true
    });

    useEffect(() => {
        if (isEdit) {
            fetchOrganizer();
        }
    }, [id]);

    const fetchOrganizer = async () => {
        try {
            const res = await axiosInstance.get(`/admin/organizers`);
            const org = res.data.find((u: any) => u.id === parseInt(id!));
            if (org) {
                setForm({
                    name: org.name || "",
                    username: org.username?.String || "",
                    organizer_name: org.organizer_name?.String || "",
                    email: org.email || "",
                    password: "",
                    phone: org.phone?.String || "",
                    about_us: org.about_us?.String || "",
                    province: org.province?.String || "",
                    city: org.city?.String || "",
                    zip_code: org.zip_code?.String || "",
                    address: org.address?.String || "",
                    is_active: org.is_active
                });
                if (org.profile_photo_path?.Valid) {
                    setPreview(`${process.env.NEXT_PUBLIC_API_URL?.replace('/api', '')}${org.profile_photo_path.String}`);
                }
            } else {
                toast.error("Organizer not found");
                router.push("/admin/organizers");
            }
        } catch (error) {
            toast.error("Failed to load organizer data");
        } finally {
            setFetching(false);
        }
    };

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            setLogoFile(file);
            setPreview(URL.createObjectURL(file));
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);

        const formData = new FormData();
        Object.entries(form).forEach(([key, value]) => {
            formData.append(key, String(value));
        });

        if (logoFile) {
            formData.append("logo", logoFile);
        }

        try {
            const url = isEdit ? `/admin/organizers?id=${id}` : "/admin/organizers";
            await axiosInstance.post(url, formData, {
                headers: { "Content-Type": "multipart/form-data" }
            });
            toast.success(isEdit ? "Organizer updated" : "Organizer created");
            router.push("/admin/organizers");
        } catch (error: any) {
            toast.error(error.response?.data || "Failed to save organizer");
        } finally {
            setLoading(false);
        }
    };

    if (fetching) return <div className="p-20 text-center text-gray-500 italic">Fetching organizer data...</div>;

    return (
        <form onSubmit={handleSubmit} className="space-y-8 max-w-5xl mx-auto pb-20">
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-4">
                    <Link href="/admin/organizers" className="p-2 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                        <ArrowLeft className="w-5 h-5" />
                    </Link>
                    <div>
                        <h2 className="text-2xl font-bold">{isEdit ? "Edit Organizer" : "Create New Organizer"}</h2>
                        <p className="text-sm text-gray-500 italic">Fill in the profile details for the event organizer.</p>
                    </div>
                </div>
                <button
                    type="submit"
                    disabled={loading}
                    className="px-6 py-2.5 bg-primary text-white font-bold rounded-xl hover:bg-primary transition-all shadow-lg shadow-primary/20 flex items-center gap-2 disabled:opacity-50"
                >
                    {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : <Save className="w-5 h-5" />}
                    {isEdit ? "Save Changes" : "Create Organizer"}
                </button>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Profile Photo / Logo */}
                <div className="space-y-6">
                    <div className="bg-(--card) p-6 rounded-2xl border border-(--card-border) shadow-sm">
                        <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-4">Organizer Logo</label>
                        <div className="flex flex-col items-center gap-4">
                            <div className="relative w-40 h-40 rounded-2xl overflow-hidden bg-gray-100 dark:bg-gray-800 border-2 border-dashed border-(--card-border) group">
                                {preview ? (
                                    <Image src={preview} alt="Preview" fill className="object-cover" />
                                ) : (
                                    <div className="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                        <Store className="w-12 h-12 mb-2 opacity-20" />
                                        <span className="text-xs italic">No Image</span>
                                    </div>
                                )}
                                <label className="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex flex-col items-center justify-center text-white cursor-pointer gap-2">
                                    <Upload className="w-6 h-6" />
                                    <span className="text-xs font-bold uppercase tracking-wider">Change Logo</span>
                                    <input type="file" className="hidden" accept="image/*" onChange={handleFileChange} />
                                </label>
                            </div>
                            <p className="text-[10px] text-gray-500 italic text-center uppercase tracking-widest">Recommended: 1:1 ratio, max 2MB</p>
                        </div>
                    </div>

                    <div className="bg-(--card) p-6 rounded-2xl border border-(--card-border) shadow-sm">
                        <label className="block text-sm font-bold text-gray-700 dark:text-gray-300 mb-2">Account Status</label>
                        <div className="flex items-center gap-2 border border-(--card-border) rounded-xl p-3 bg-(--background)">
                            <input
                                type="checkbox"
                                id="is_active"
                                checked={form.is_active}
                                onChange={(e) => setForm({ ...form, is_active: e.target.checked })}
                                className="w-5 h-5 accent-primary cursor-pointer"
                            />
                            <label htmlFor="is_active" className="text-sm font-medium cursor-pointer">Active and Visible</label>
                        </div>
                    </div>
                </div>

                {/* Information Fields */}
                <div className="lg:col-span-2 space-y-6">
                    <div className="bg-(--card) p-8 rounded-2xl border border-(--card-border) shadow-sm space-y-6">
                        <h3 className="text-lg font-bold border-b border-(--card-border) pb-4 mb-6">Basic Information</h3>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <label className="text-sm font-bold opacity-70">Organizer Name (Corporate)</label>
                                <Input
                                    placeholder="e.g. Ingate Studio"
                                    value={form.organizer_name}
                                    onChange={(e) => setForm({ ...form, organizer_name: e.target.value })}
                                />
                            </div>
                            <div className="space-y-2">
                                <label className="text-sm font-bold opacity-70">Contact Person Name</label>
                                <Input
                                    placeholder="e.g. John Doe"
                                    value={form.name}
                                    onChange={(e) => setForm({ ...form, name: e.target.value })}
                                    required
                                />
                            </div>
                            <div className="space-y-2">
                                <label className="text-sm font-bold opacity-70">Unique Username</label>
                                <Input
                                    placeholder="e.g. ingate_studio"
                                    value={form.username}
                                    onChange={(e) => setForm({ ...form, username: e.target.value })}
                                />
                            </div>
                            <div className="space-y-2">
                                <label className="text-sm font-bold opacity-70">Phone Number</label>
                                <Input
                                    placeholder="0812...."
                                    value={form.phone}
                                    onChange={(e) => setForm({ ...form, phone: e.target.value })}
                                />
                            </div>
                        </div>

                        <div className="space-y-2">
                            <label className="text-sm font-bold opacity-70">About Organizer</label>
                            <Textarea
                                placeholder="Describe the organizer history, expertise, etc."
                                value={form.about_us}
                                onChange={(e) => setForm({ ...form, about_us: e.target.value })}
                                rows={4}
                            />
                        </div>
                    </div>

                    <div className="bg-(--card) p-8 rounded-2xl border border-(--card-border) shadow-sm space-y-6">
                        <h3 className="text-lg font-bold border-b border-(--card-border) pb-4 mb-6">Authentication</h3>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-2">
                                <label className="text-sm font-bold opacity-70">Email Address</label>
                                <Input
                                    type="email"
                                    placeholder="organizer@example.com"
                                    value={form.email}
                                    onChange={(e) => setForm({ ...form, email: e.target.value })}
                                    required
                                />
                            </div>
                            <div className="space-y-2">
                                <label className="text-sm font-bold opacity-70">Password {isEdit && <span className="text-[10px] font-normal italic opacity-50">(Leave blank to keep current)</span>}</label>
                                <Input
                                    type="password"
                                    placeholder="••••••••"
                                    value={form.password}
                                    onChange={(e) => setForm({ ...form, password: e.target.value })}
                                    required={!isEdit}
                                />
                            </div>
                        </div>
                    </div>

                    <div className="bg-(--card) p-8 rounded-2xl border border-(--card-border) shadow-sm space-y-6">
                        <h3 className="text-lg font-bold border-b border-(--card-border) pb-4 mb-6">Location Details</h3>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div className="space-y-2">
                                <label className="text-sm font-bold opacity-70">Province</label>
                                <Input
                                    placeholder="e.g. West Java"
                                    value={form.province}
                                    onChange={(e) => setForm({ ...form, province: e.target.value })}
                                />
                            </div>
                            <div className="space-y-2">
                                <label className="text-sm font-bold opacity-70">City</label>
                                <Input
                                    placeholder="e.g. Bandung"
                                    value={form.city}
                                    onChange={(e) => setForm({ ...form, city: e.target.value })}
                                />
                            </div>
                            <div className="space-y-2">
                                <label className="text-sm font-bold opacity-70">Zip Code</label>
                                <Input
                                    placeholder="e.g. 40123"
                                    value={form.zip_code}
                                    onChange={(e) => setForm({ ...form, zip_code: e.target.value })}
                                />
                            </div>
                        </div>
                        <div className="space-y-2">
                            <label className="text-sm font-bold opacity-70">Full Address</label>
                            <Textarea
                                placeholder="Complete street address..."
                                value={form.address}
                                onChange={(e) => setForm({ ...form, address: e.target.value })}
                                rows={2}
                            />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    );
}
