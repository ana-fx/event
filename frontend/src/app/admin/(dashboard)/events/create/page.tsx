"use client";

import { useState } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import axiosInstance from "@/lib/axios";
import { toast } from "react-hot-toast";
import { ChevronLeft, Upload, Loader2, Save } from "lucide-react";
import Image from "next/image";
import { Select } from "@/components/ui/Select";
import { DatePicker } from "@/components/ui/DatePicker";
import RichTextEditor from "@/components/ui/RichTextEditor";

export default function CreateEvent() {
    const router = useRouter();
    const [loading, setLoading] = useState(false);

    // Basic Fields
    const [formData, setFormData] = useState({
        name: "",
        category: "",
        start_date: "",
        end_date: "",
        location: "",
        province: "",
        city: "",
        zip: "",
        google_map_embed: "",
        description: "",
        terms: "",
        status: "draft",
        organizer_name: "",
        seo_title: "",
        seo_description: "",
    });

    // Files
    const [banner, setBanner] = useState<File | null>(null);
    const [thumbnail, setThumbnail] = useState<File | null>(null);
    const [organizerLogo, setOrganizerLogo] = useState<File | null>(null);

    // Previews
    const [previews, setPreviews] = useState({
        banner: "",
        thumbnail: "",
        organizerLogo: "",
    });

    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>, field: "banner" | "thumbnail" | "organizerLogo") => {
        const file = e.target.files?.[0];
        if (file) {
            if (field === "banner") setBanner(file);
            if (field === "thumbnail") setThumbnail(file);
            if (field === "organizerLogo") setOrganizerLogo(file);

            const url = URL.createObjectURL(file);
            setPreviews(prev => ({ ...prev, [field]: url }));
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);

        try {
            // Use FormData for file upload
            const data = new FormData();

            // Append basic fields
            Object.entries(formData).forEach(([key, value]) => {
                data.append(key, value);
            });

            // Append files if they exist
            if (banner) data.append("banner", banner);
            if (thumbnail) data.append("thumbnail", thumbnail);
            if (organizerLogo) data.append("organizer_logo", organizerLogo);

            // Default fees for now (Phase 2 will add fee config)
            data.append("reseller_fee_type", "fixed");
            data.append("reseller_fee_value", "0");
            data.append("organizer_fee_online_type", "fixed");
            data.append("organizer_fee_online_value", "0");

            await axiosInstance.post("/admin/events", data, {
                headers: { "Content-Type": "multipart/form-data" },
            });

            toast.success("Event created successfully!");
            router.push("/admin/events");
        } catch (error) {
            console.error(error);
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            toast.error((error as any).response?.data?.error || "Failed to create event");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="max-w-4xl mx-auto pb-20">
            <div className="flex items-center gap-4 mb-8">
                <Link
                    href="/admin/events"
                    className="p-2 hover:bg-gray-100 rounded-lg text-gray-500 transition-colors"
                >
                    <ChevronLeft className="w-5 h-5" />
                </Link>
                <div>
                    <h2 className="text-2xl font-bold text-gray-900">Create New Event</h2>
                    <p className="text-gray-500 text-sm">Fill in the details to publish a new event.</p>
                </div>
            </div>

            <form onSubmit={handleSubmit} className="space-y-8">
                {/* 1. Basic Info */}
                <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                    <h3 className="text-lg font-bold text-gray-900 border-b border-gray-100 pb-4">Basic Details</h3>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div className="md:col-span-2">
                            <label className="block text-sm font-bold text-gray-700 mb-2">Event Name</label>
                            <input
                                type="text"
                                required
                                className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                value={formData.name}
                                onChange={(e) => setFormData({ ...formData, name: e.target.value })}
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-bold text-gray-700 mb-2">Category</label>
                            <input
                                type="text"
                                required
                                className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                value={formData.category}
                                onChange={(e) => setFormData({ ...formData, category: e.target.value })}
                            />
                        </div>

                        <div>
                            <Select
                                label="Status"
                                value={formData.status}
                                onChange={(val) => setFormData({ ...formData, status: val })}
                                options={[
                                    { label: "Draft", value: "draft" },
                                    { label: "Active", value: "active" }
                                ]}
                            />
                        </div>

                        <div className="md:col-span-2">
                            <RichTextEditor
                                label="Description"
                                value={formData.description}
                                onChange={(val) => setFormData({ ...formData, description: val })}
                            />
                        </div>

                        <div className="md:col-span-2">
                            <label className="block text-sm font-bold text-gray-700 mb-2">Maps Embed (HTML)</label>
                            <textarea
                                rows={3}
                                className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all font-mono text-xs"
                                value={formData.google_map_embed || ''}
                                onChange={(e) => setFormData({ ...formData, google_map_embed: e.target.value })}
                                placeholder='<iframe src="..."></iframe>'
                            ></textarea>
                        </div>

                        <div className="md:col-span-2">
                            <RichTextEditor
                                label="Terms & Conditions"
                                value={formData.terms}
                                onChange={(val) => setFormData({ ...formData, terms: val })}
                            />
                        </div>
                    </div>
                </div>

                {/* 2. Media Uploads */}
                <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                    <h3 className="text-lg font-bold text-gray-900 border-b border-gray-100 pb-4">Media</h3>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {/* Banner */}
                        <div>
                            <label className="block text-sm font-bold text-gray-700 mb-2">Event Banner</label>
                            <div className="border-2 border-dashed border-gray-300 rounded-xl aspect-video relative flex items-center justify-center bg-gray-50 hover:border-blue-500 transition-colors cursor-pointer overflow-hidden group">
                                {previews.banner ? (
                                    <img src={previews.banner} alt="Preview" className="w-full h-full object-cover" />
                                ) : (
                                    <div className="text-center p-4">
                                        <Upload className="w-8 h-8 text-gray-400 mx-auto mb-2" />
                                        <p className="text-sm text-gray-500">Click to upload banner</p>
                                        <p className="text-xs text-gray-400 mt-1">16:9 recommended</p>
                                    </div>
                                )}
                                <input
                                    type="file"
                                    accept="image/*"
                                    className="absolute inset-0 opacity-0 cursor-pointer"
                                    onChange={(e) => handleFileChange(e, "banner")}
                                />
                            </div>
                        </div>

                        {/* Thumbnail */}
                        <div>
                            <label className="block text-sm font-bold text-gray-700 mb-2">Thumbnail (Square)</label>
                            <div className="border-2 border-dashed border-gray-300 rounded-xl aspect-square w-48 relative flex items-center justify-center bg-gray-50 hover:border-blue-500 transition-colors cursor-pointer overflow-hidden">
                                {previews.thumbnail ? (
                                    <img src={previews.thumbnail} alt="Preview" className="w-full h-full object-cover" />
                                ) : (
                                    <div className="text-center p-4">
                                        <Upload className="w-8 h-8 text-gray-400 mx-auto mb-2" />
                                        <p className="text-sm text-gray-500">Upload thumbnail</p>
                                    </div>
                                )}
                                <input
                                    type="file"
                                    accept="image/*"
                                    className="absolute inset-0 opacity-0 cursor-pointer"
                                    onChange={(e) => handleFileChange(e, "thumbnail")}
                                />
                            </div>
                        </div>

                        {/* Organizer Logo */}
                        <div>
                            <label className="block text-sm font-bold text-gray-700 mb-2">Organizer Logo</label>
                            <div className="border-2 border-dashed border-gray-300 rounded-xl aspect-square w-48 relative flex items-center justify-center bg-gray-50 hover:border-blue-500 transition-colors cursor-pointer overflow-hidden">
                                {previews.organizerLogo ? (
                                    <img src={previews.organizerLogo} alt="Preview" className="w-full h-full object-cover" />
                                ) : (
                                    <div className="text-center p-4">
                                        <Upload className="w-8 h-8 text-gray-400 mx-auto mb-2" />
                                        <p className="text-sm text-gray-500">Upload logo</p>
                                    </div>
                                )}
                                <input
                                    type="file"
                                    accept="image/*"
                                    className="absolute inset-0 opacity-0 cursor-pointer"
                                    onChange={(e) => handleFileChange(e, "organizerLogo")}
                                />
                            </div>
                        </div>

                        <div className="md:col-span-2">
                            <label className="block text-sm font-bold text-gray-700 mb-2">Organizer Name</label>
                            <input
                                type="text"
                                className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                value={formData.organizer_name}
                                onChange={(e) => setFormData({ ...formData, organizer_name: e.target.value })}
                            />
                        </div>
                    </div>
                </div>

                {/* 3. Dates & Location */}
                <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                    <h3 className="text-lg font-bold text-gray-900 border-b border-gray-100 pb-4">Date & Location</h3>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <DatePicker
                                label="Start Date (UTC)"
                                value={formData.start_date}
                                onChange={(val) => setFormData({ ...formData, start_date: val })}
                            />
                        </div>
                        <div>
                            <DatePicker
                                label="End Date (UTC)"
                                value={formData.end_date}
                                onChange={(val) => setFormData({ ...formData, end_date: val })}
                            />
                        </div>

                        <div className="md:col-span-2">
                            <label className="block text-sm font-bold text-gray-700 mb-2">Venue Name</label>
                            <input
                                type="text"
                                placeholder="e.g. Jakarta Convention Center"
                                className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                value={formData.location}
                                onChange={(e) => setFormData({ ...formData, location: e.target.value })}
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-gray-700 mb-2">City</label>
                            <input
                                type="text"
                                className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                value={formData.city}
                                onChange={(e) => setFormData({ ...formData, city: e.target.value })}
                            />
                        </div>

                        <div>
                            <label className="block text-sm font-bold text-gray-700 mb-2">Province</label>
                            <input
                                type="text"
                                className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                value={formData.province}
                                onChange={(e) => setFormData({ ...formData, province: e.target.value })}
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-gray-700 mb-2">ZIP Code</label>
                            <input
                                type="text"
                                className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                value={formData.zip}
                                onChange={(e) => setFormData({ ...formData, zip: e.target.value })}
                            />
                        </div>
                    </div>
                </div>

                {/* 4. SEO */}
                <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm space-y-6">
                    <h3 className="text-lg font-bold text-gray-900 border-b border-gray-100 pb-4">SEO Settings</h3>
                    <div className="grid grid-cols-1 gap-6">
                        <div>
                            <label className="block text-sm font-bold text-gray-700 mb-2">Meta Title</label>
                            <input
                                type="text"
                                className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                value={formData.seo_title}
                                onChange={(e) => setFormData({ ...formData, seo_title: e.target.value })}
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-gray-700 mb-2">Meta Description</label>
                            <textarea
                                rows={2}
                                className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all"
                                value={formData.seo_description}
                                onChange={(e) => setFormData({ ...formData, seo_description: e.target.value })}
                            ></textarea>
                        </div>

                        {/* Submit Actions */}
                        <div className="flex justify-end gap-4 pt-4">
                            <Link
                                href="/admin/events"
                                className="px-6 py-3 rounded-xl border border-gray-200 text-gray-600 font-bold hover:bg-gray-50 transition-colors"
                            >
                                Cancel
                            </Link>
                            <button
                                type="submit"
                                disabled={loading}
                                className="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-colors shadow-lg shadow-blue-600/20 disabled:opacity-70 flex items-center gap-2"
                            >
                                {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : <Save className="w-5 h-5" />}
                                Create Event
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    );
}
