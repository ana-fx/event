"use client";

import { useEffect, useState } from "react";
import axiosInstance from "@/lib/axios";
import { Save, User, Mail, Shield, Smartphone, MapPin, Info, Image as ImageIcon, Loader2, AlertCircle } from "lucide-react";
import { toast } from "react-hot-toast";

export default function OrganizerSettingsContent() {
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [profile, setProfile] = useState<any>(null);
    const [logoPreview, setLogoPreview] = useState<string | null>(null);

    const fetchProfile = async () => {
        try {
            const res = await axiosInstance.get("/organizer/profile");
            setProfile(res.data);
            if (res.data.profile_photo_path?.String) {
                setLogoPreview(`${process.env.NEXT_PUBLIC_STORAGE_URL}/${res.data.profile_photo_path.String}`);
            }
        } catch (error) {
            toast.error("Failed to load profile");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchProfile();
    }, []);

    const handleSubmit = async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();
        setSaving(true);
        const formData = new FormData(e.currentTarget);

        try {
            await axiosInstance.post("/organizer/profile/update", formData);
            toast.success("Profile updated successfully");
            fetchProfile();
        } catch (error) {
            toast.error("Failed to update profile");
        } finally {
            setSaving(false);
        }
    };

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        const file = e.target.files?.[0];
        if (file) {
            const reader = new FileReader();
            reader.onloadend = () => {
                setLogoPreview(reader.result as string);
            };
            reader.readAsDataURL(file);
        }
    };

    if (loading) return (
        <div className="flex justify-center items-center min-h-[400px]">
            <Loader2 className="w-8 h-8 animate-spin text-primary" />
        </div>
    );

    return (
        <div className="p-4 md:p-8 animate-fade-in max-w-4xl mx-auto">
            <div className="mb-8">
                <h1 className="text-3xl font-bold font-heading text-(--foreground)">Organizer Settings</h1>
                <p className="text-gray-500 mt-1">Manage your organizer profile and account security.</p>
            </div>

            <form onSubmit={handleSubmit} className="space-y-8">
                {/* Profile Section */}
                <div className="bg-(--card) rounded-2xl border border-(--card-border) shadow-xl overflow-hidden">
                    <div className="p-6 border-b border-(--card-border) bg-(--background)/50">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shadow-inner">
                                <User className="w-5 h-5" />
                            </div>
                            <h2 className="text-lg font-bold uppercase tracking-widest text-(--foreground)">Public Profile</h2>
                        </div>
                    </div>

                    <div className="p-6 space-y-6">
                        <div className="flex flex-col md:flex-row gap-8 items-start">
                            <div className="flex flex-col items-center gap-4">
                                <div className="relative group">
                                    <div className="w-32 h-32 rounded-3xl bg-(--background) border-2 border-dashed border-(--card-border) flex items-center justify-center overflow-hidden transition-all group-hover:border-primary shadow-inner group-hover:shadow-primary/10">
                                        {logoPreview ? (
                                            <img src={logoPreview} alt="Logo Preview" className="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" />
                                        ) : (
                                            <ImageIcon className="w-8 h-8 text-gray-400 group-hover:text-primary transition-colors" />
                                        )}
                                        <div className="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                            <p className="text-white text-[10px] font-black uppercase tracking-widest">Change Logo</p>
                                        </div>
                                    </div>
                                    <input type="file" name="logo" onChange={handleFileChange} className="absolute inset-0 opacity-0 cursor-pointer" accept="image/*" />
                                </div>
                                <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">Organizer Logo</p>
                            </div>

                            <div className="flex-1 grid grid-cols-1 md:grid-cols-2 gap-4 w-full">
                                <div className="space-y-2">
                                    <label className="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Personal Name</label>
                                    <div className="relative group">
                                        <User className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-primary transition-colors" />
                                        <input
                                            name="name"
                                            defaultValue={profile?.name}
                                            className="w-full pl-10 pr-4 py-3 rounded-xl border border-(--card-border) focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-(--background) font-medium"
                                            placeholder="Enter your name"
                                        />
                                    </div>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Organizer / Business Name</label>
                                    <div className="relative group">
                                        <Shield className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-primary transition-colors" />
                                        <input
                                            name="organizer_name"
                                            defaultValue={profile?.organizer_name?.String}
                                            className="w-full pl-10 pr-4 py-3 rounded-xl border border-(--card-border) focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-(--background) font-medium"
                                            placeholder="Enter organizer name"
                                        />
                                    </div>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Phone Number</label>
                                    <div className="relative group">
                                        <Smartphone className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 group-focus-within:text-primary transition-colors" />
                                        <input
                                            name="phone"
                                            defaultValue={profile?.phone?.String}
                                            className="w-full pl-10 pr-4 py-3 rounded-xl border border-(--card-border) focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-(--background) font-medium"
                                            placeholder="Enter phone number"
                                        />
                                    </div>
                                </div>
                                <div className="space-y-2">
                                    <label className="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">Email (Read-only)</label>
                                    <div className="relative group">
                                        <Mail className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-300" />
                                        <input
                                            readOnly
                                            value={profile?.email}
                                            className="w-full pl-10 pr-4 py-3 rounded-xl border border-(--card-border) bg-gray-50/50 dark:bg-gray-900/50 text-gray-400 cursor-not-allowed font-medium"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div className="space-y-2">
                            <label className="text-xs font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                                <Info className="w-3 h-3 text-primary" />
                                About Organizer
                            </label>
                            <textarea
                                name="about_us"
                                defaultValue={profile?.about_us?.String}
                                rows={4}
                                className="w-full px-4 py-3 rounded-xl border border-(--card-border) focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-(--background) font-medium resize-none"
                                placeholder="Tell people about your organization..."
                            />
                        </div>

                        <div className="space-y-2">
                            <label className="text-xs font-black text-gray-400 uppercase tracking-widest ml-1 flex items-center gap-2">
                                <MapPin className="w-3 h-3 text-primary" />
                                Address
                            </label>
                            <input
                                name="address"
                                defaultValue={profile?.address?.String}
                                className="w-full px-4 py-3 rounded-xl border border-(--card-border) focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-(--background) font-medium"
                                placeholder="Enter organization address"
                            />
                        </div>
                    </div>
                </div>

                {/* Password Section */}
                <div className="bg-(--card) rounded-2xl border border-(--card-border) shadow-xl overflow-hidden">
                    <div className="p-6 border-b border-(--card-border) bg-(--background)/50">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-amber-500/10 flex items-center justify-center text-amber-500 shadow-inner">
                                <Shield className="w-5 h-5" />
                            </div>
                            <h2 className="text-lg font-bold uppercase tracking-widest text-(--foreground)">Update Password</h2>
                        </div>
                    </div>
                    <div className="p-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div className="space-y-2 text-sm text-gray-500">
                                <p>Leave password field empty if you don&apos;t want to change it.</p>
                                <p className="italic text-xs font-medium bg-amber-50 dark:bg-amber-950/20 p-2 rounded-lg border border-amber-200/50 dark:border-amber-900/50 text-amber-600 dark:text-amber-500 underline-offset-2">
                                    <AlertCircle className="w-3 h-3 inline mr-1" />
                                    Security tip: Use at least 8 characters with a mix of letters and numbers.
                                </p>
                            </div>
                            <div className="space-y-2">
                                <label className="text-xs font-black text-gray-400 uppercase tracking-widest ml-1">New Password</label>
                                <input
                                    type="password"
                                    name="password"
                                    className="w-full px-4 py-3 rounded-xl border border-(--card-border) focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-(--background) font-medium"
                                    placeholder="Enter new password"
                                />
                            </div>
                        </div>
                    </div>
                </div>

                <div className="flex justify-end pt-4 pb-8">
                    <button
                        type="submit"
                        disabled={saving}
                        className="flex items-center gap-3 px-8 py-4 bg-primary text-white font-black rounded-2xl hover:bg-primary transition-all shadow-xl shadow-primary/30 active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed uppercase tracking-widest"
                    >
                        {saving ? (
                            <Loader2 className="w-5 h-5 animate-spin" />
                        ) : (
                            <Save className="w-5 h-5" />
                        )}
                        {saving ? "Saving Changes..." : "Save Profile Settings"}
                    </button>
                </div>
            </form>
        </div>
    );
}
