"use client";

import { useEffect, useState, Suspense } from "react";
import { useRouter, useSearchParams } from "next/navigation";
import Link from "next/link";
import axiosInstance from "@/lib/axios";
import { toast } from "react-hot-toast";
import { ChevronLeft, Upload, Loader2, Save, Trash, Plus, ScanBarcode, Store } from "lucide-react";
import Tabs from "@/components/ui/Tabs";
import { Select } from "@/components/ui/Select";
import { DatePicker } from "@/components/ui/DatePicker";
import RichTextEditor from "@/components/ui/RichTextEditor";
import { getImageUrl } from "@/lib/utils";

// --- Types ---
interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

interface AssignedReseller extends User {
    commission_type: string;
    commission_value: number;
}

interface Ticket {
    id: number;
    event_id: number;
    name: string;
    description: string;
    price: number;
    quota: number;
    max_purchase_per_user: number;
    start_date: string;
    end_date: string;
    is_active: boolean;
}

// --- TAB 1: DETAILS ---
// eslint-disable-next-line @typescript-eslint/no-explicit-any
function EventDetailsTab({ id, initialData, refresh }: { id: string, initialData: any, refresh: () => void }) {
    const [loading, setLoading] = useState(false);
    const [formData, setFormData] = useState(initialData || {
        name: "", category: "", start_date: "", end_date: "",
        location: "", province: "", city: "", zip: "",
        google_map_embed: "",
        description: "", terms: "", status: "draft",
        organizer_name: "", seo_title: "", seo_description: ""
    });

    const [previews, setPreviews] = useState({
        banner: getImageUrl(initialData?.banner_path),
        thumbnail: getImageUrl(initialData?.thumbnail_path),
        organizerLogo: getImageUrl(initialData?.organizer_logo_path),
    });

    const [files, setFiles] = useState<{ banner?: File, thumbnail?: File, organizerLogo?: File }>({});

    useEffect(() => {
        if (initialData) {
            setFormData({
                name: initialData.name || "",
                category: initialData.category || "",
                start_date: initialData.start_date || "",
                end_date: initialData.end_date || "",
                location: initialData.location || "",
                province: initialData.province || "",
                city: initialData.city || "",
                zip: initialData.zip || "",
                google_map_embed: initialData.google_map_embed || "",
                description: initialData.description || "",
                terms: initialData.terms || "",
                status: initialData.status || "draft",
                organizer_name: initialData.organizer_name || "",
                seo_title: initialData.seo_title || "",
                seo_description: initialData.seo_description || ""
            });
            setPreviews({
                banner: getImageUrl(initialData.banner_path),
                thumbnail: getImageUrl(initialData.thumbnail_path),
                organizerLogo: getImageUrl(initialData.organizer_logo_path),
            });
        }
    }, [initialData]);


    const handleFileChange = (e: React.ChangeEvent<HTMLInputElement>, field: "banner" | "thumbnail" | "organizerLogo") => {
        const file = e.target.files?.[0];
        if (file) {
            setFiles(prev => ({ ...prev, [field]: file }));
            setPreviews(prev => ({ ...prev, [field]: URL.createObjectURL(file) }));
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        try {
            const data = new FormData();
            Object.entries(formData).forEach(([key, value]) => {
                data.append(key, String(value || ""));
            });
            if (files.banner) data.append("banner", files.banner);
            if (files.thumbnail) data.append("thumbnail", files.thumbnail);
            if (files.organizerLogo) data.append("organizer_logo", files.organizerLogo);

            // Default fees if missing
            if (!formData.reseller_fee_type) {
                data.append("reseller_fee_type", "fixed");
                data.append("reseller_fee_value", "0");
                data.append("organizer_fee_online_type", "fixed");
                data.append("organizer_fee_online_value", "0");
            }

            await axiosInstance.put(`/admin/events?id=${id}`, data, {
                headers: { "Content-Type": "multipart/form-data" },
            });
            toast.success("Event updated successfully!");
            refresh();
        } catch (error) {
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            toast.error((error as any).response?.data?.error || "Failed to update event");
        } finally {
            setLoading(false);
        }
    };

    return (
        <form onSubmit={handleSubmit} className="space-y-8 mt-6">
            <div className="bg-(--card) p-6 rounded-2xl border border-(--card-border) shadow-sm space-y-6">
                <h3 className="text-lg font-bold text-(--foreground) border-b border-(--card-border) pb-4">Basic Details</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="md:col-span-2">
                        <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Event Name</label>
                        <input type="text" required className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all bg-(--background) text-(--foreground) placeholder:opacity-50" value={formData.name} onChange={(e) => setFormData({ ...formData, name: e.target.value })} />
                    </div>
                    <div>
                        <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Category</label>
                        <input type="text" required className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all bg-(--background) text-(--foreground) placeholder:opacity-50" value={formData.category} onChange={(e) => setFormData({ ...formData, category: e.target.value })} />
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
                        <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Maps Embed (HTML)</label>
                        <textarea
                            rows={3}
                            className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) bg-(--background) text-(--foreground) focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all font-mono text-xs"
                            value={formData.google_map_embed || ''}
                            onChange={(e) => setFormData({ ...formData, google_map_embed: e.target.value })}
                            placeholder='<iframe src="..."></iframe>'
                        ></textarea>
                        {formData.google_map_embed && (
                            <div className="mt-4 rounded-xl overflow-hidden border border-(--card-border) aspect-video bg-gray-100">
                                <div dangerouslySetInnerHTML={{ __html: formData.google_map_embed }} className="w-full h-full [&>iframe]:w-full [&>iframe]:h-full" />
                            </div>
                        )}
                    </div>

                    <div className="md:col-span-2">
                        <RichTextEditor
                            label="Terms & Conditions"
                            value={formData.terms || ''}
                            onChange={(val) => setFormData({ ...formData, terms: val })}
                        />
                    </div>
                </div>
            </div>

            {/* Media Section */}
            <div className="bg-(--card) p-6 rounded-2xl border border-(--card-border) shadow-sm space-y-6">
                <h3 className="text-lg font-bold text-(--foreground) border-b border-(--card-border) pb-4">Media</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Banner</label>
                        <div className="border-2 border-dashed border-(--card-border) rounded-xl aspect-video relative flex items-center justify-center bg-(--background) overflow-hidden">
                            {previews.banner && <img src={previews.banner} className="w-full h-full object-cover" />}
                            <input type="file" accept="image/*" className="absolute inset-0 opacity-0 cursor-pointer" onChange={(e) => handleFileChange(e, "banner")} />
                            {!previews.banner && <span className="text-gray-400">Upload Banner</span>}
                        </div>
                    </div>
                    <div>
                        <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Thumbnail</label>
                        <div className="border-2 border-dashed border-(--card-border) rounded-xl aspect-square w-48 relative flex items-center justify-center bg-(--background) overflow-hidden">
                            {previews.thumbnail && <img src={previews.thumbnail} className="w-full h-full object-cover" />}
                            <input type="file" accept="image/*" className="absolute inset-0 opacity-0 cursor-pointer" onChange={(e) => handleFileChange(e, "thumbnail")} />
                            {!previews.thumbnail && <span className="text-gray-400 text-sm">Upload</span>}
                        </div>
                    </div>

                    <div>
                        <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Organizer Logo</label>
                        <div className="border-2 border-dashed border-(--card-border) rounded-xl aspect-square w-48 relative flex items-center justify-center bg-(--background) hover:border-blue-500 transition-colors cursor-pointer overflow-hidden">
                            {previews.organizerLogo ? (
                                <img src={previews.organizerLogo} alt="Preview" className="w-full h-full object-cover" />
                            ) : (
                                <div className="text-center p-4">
                                    <Upload className="w-8 h-8 text-gray-400 mx-auto mb-2" />
                                    <p className="text-sm text-gray-500 opacity-60">Upload logo</p>
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
                        <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Organizer Name</label>
                        <input
                            type="text"
                            className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) bg-(--background) text-(--foreground) focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all"
                            value={formData.organizer_name || ''}
                            onChange={(e) => setFormData({ ...formData, organizer_name: e.target.value })}
                        />
                    </div>
                </div>
            </div>

            {/* Date & Location */}
            <div className="bg-(--card) p-6 rounded-2xl border border-(--card-border) shadow-sm space-y-6">
                <h3 className="text-lg font-bold text-(--foreground) border-b border-(--card-border) pb-4">Date & Location</h3>
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div><DatePicker label="Start Date" value={formData.start_date} onChange={(val) => setFormData({ ...formData, start_date: val })} /></div>
                    <div><DatePicker label="End Date" value={formData.end_date} onChange={(val) => setFormData({ ...formData, end_date: val })} /></div>
                    <div className="md:col-span-2"><label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Location</label><input type="text" className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all bg-(--background) text-(--foreground)" value={formData.location} onChange={(e) => setFormData({ ...formData, location: e.target.value })} /></div>
                    <div><label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">City</label><input type="text" className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all bg-(--background) text-(--foreground)" value={formData.city} onChange={(e) => setFormData({ ...formData, city: e.target.value })} /></div>

                    <div>
                        <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Province</label>
                        <input
                            type="text"
                            className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) bg-(--background) text-(--foreground) focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all"
                            value={formData.province || ''}
                            onChange={(e) => setFormData({ ...formData, province: e.target.value })}
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">ZIP Code</label>
                        <input
                            type="text"
                            className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) bg-(--background) text-(--foreground) focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all"
                            value={formData.zip || ''}
                            onChange={(e) => setFormData({ ...formData, zip: e.target.value })}
                        />
                    </div>
                </div>
            </div>

            {/* 4. SEO */}
            <div className="bg-(--card) p-6 rounded-2xl border border-(--card-border) shadow-sm space-y-6">
                <h3 className="text-lg font-bold text-(--foreground) border-b border-(--card-border) pb-4">SEO Settings</h3>
                <div className="grid grid-cols-1 gap-6">
                    <div>
                        <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Meta Title</label>
                        <input
                            type="text"
                            className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) bg-(--background) text-(--foreground) focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all"
                            value={formData.seo_title || ''}
                            onChange={(e) => setFormData({ ...formData, seo_title: e.target.value })}
                        />
                    </div>
                    <div>
                        <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Meta Description</label>
                        <textarea
                            rows={2}
                            className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) bg-(--background) text-(--foreground) focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all"
                            value={formData.seo_description || ''}
                            onChange={(e) => setFormData({ ...formData, seo_description: e.target.value })}
                        ></textarea>
                    </div>
                </div>
            </div>

            <div className="flex justify-end pt-4">
                <button type="submit" disabled={loading} className="px-4 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-sm shadow-blue-600/20 disabled:opacity-70 flex items-center justify-center">
                    {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : <Save className="w-5 h-5" />}
                    Save Changes
                </button>
            </div>
        </form>
    );
}

// --- TAB 2: ASSIGNMENTS ---
function AssignmentsTab({ eventId }: { eventId: string }) {
    const [scanners, setScanners] = useState<User[]>([]);
    const [resellers, setResellers] = useState<AssignedReseller[]>([]);
    const [availableUsers, setAvailableUsers] = useState<User[]>([]);
    const [loading, setLoading] = useState(true);

    const [selectedScanner, setSelectedScanner] = useState("");
    const [selectedReseller, setSelectedReseller] = useState("");
    const [commissionType, setCommissionType] = useState("fixed");
    const [commissionValue, setCommissionValue] = useState("0");

    const fetchData = async () => {
        setLoading(true);
        try {
            const [scannersRes, resellersRes, usersRes] = await Promise.all([
                axiosInstance.get(`/admin/events/assign-scanner?event_id=${eventId}`),
                axiosInstance.get(`/admin/events/assign-reseller?event_id=${eventId}`),
                axiosInstance.get(`/admin/users`)
            ]);
            setScanners(scannersRes.data || []);
            setResellers(resellersRes.data || []);
            setAvailableUsers(usersRes.data || []);
        } catch (error) {
            toast.error("Failed to load assignments");
        } finally {
            setLoading(false);
        }
    };

    // eslint-disable-next-line react-hooks/exhaustive-deps
    useEffect(() => { fetchData(); }, [eventId]);

    const handleAssignScanner = async () => {
        if (!selectedScanner) return;
        try {
            await axiosInstance.post("/admin/events/assign-scanner", {
                event_id: Number(eventId),
                user_id: Number(selectedScanner)
            });
            toast.success("Scanner assigned");
            fetchData();
            setSelectedScanner("");
        } catch { toast.error("Failed to assign scanner"); }
    };

    const handleAssignReseller = async () => {
        if (!selectedReseller) return;
        try {
            await axiosInstance.post("/admin/events/assign-reseller", {
                event_id: Number(eventId),
                user_id: Number(selectedReseller),
                commission_type: commissionType,
                commission_value: Number(commissionValue)
            });
            toast.success("Reseller assigned");
            fetchData();
            setSelectedReseller("");
        } catch { toast.error("Failed to assign reseller"); }
    };

    const handleUnassign = async (endpoint: string, userId: number) => {
        if (!confirm("Are you sure?")) return;
        try {
            await axiosInstance.delete(`${endpoint}?event_id=${eventId}&user_id=${userId}`);
            toast.success("Unassigned successfully");
            fetchData();
        } catch { toast.error("Failed to unassign"); }
    };

    if (loading) return <div>Loading assignments...</div>;

    const availableScannersList = availableUsers.filter(u => u.role === 'scanner' && !scanners.find(s => s.id === u.id));
    const availableResellersList = availableUsers.filter(u => u.role === 'reseller' && !resellers.find(r => r.id === u.id));

    return (
        <div className="space-y-8 mt-8 animate-in fade-in slide-in-from-bottom-4 duration-500">
            {/* Scanners Section */}
            <div className="relative group">
                <div className="absolute -inset-0.5 bg-linear-to-r from-blue-600 to-indigo-600 rounded-2xl opacity-20 group-hover:opacity-30 transition duration-500 blur-sm"></div>
                <div className="relative bg-(--card) p-8 rounded-2xl border border-(--card-border) shadow-xl">
                    <div className="flex justify-between items-center mb-6 border-b border-(--card-border) pb-4">
                        <div>
                            <h3 className="text-xl font-bold text-(--foreground)">Assigned Scanners</h3>
                            <p className="text-sm text-gray-500 mt-1">Manage personnel who can scan tickets for this event.</p>
                        </div>
                        <div className="w-10 h-10 bg-blue-500/10 rounded-full flex items-center justify-center text-blue-500">
                            <ScanBarcode className="w-5 h-5" />
                        </div>
                    </div>

                    <div className="flex flex-col md:flex-row gap-4 mb-8 bg-(--background) p-4 rounded-xl border border-(--card-border)">
                        <div className="flex-1 relative">
                            <Select
                                value={selectedScanner}
                                onChange={(val) => setSelectedScanner(val)}
                                className="py-3 rounded-xl border-blue-200 focus:border-blue-500"
                                containerClassName="mb-1"
                                placeholder="Select a scanner to assign..."
                                options={availableScannersList.map(u => ({ label: `${u.name} — ${u.email}`, value: String(u.id) }))}
                            />
                        </div>
                        <button
                            onClick={handleAssignScanner}
                            className="px-6 py-3 bg-blue-600 text-white font-bold rounded-xl hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/30 disabled:opacity-70 disabled:shadow-none flex items-center justify-center gap-2 whitespace-nowrap active:scale-95"
                            disabled={!selectedScanner}
                        >
                            <Plus className="w-5 h-5" /> Assign Scanner
                        </button>
                    </div>

                    <div className="space-y-3">
                        {scanners.map(s => (
                            <div key={s.id} className="flex items-center justify-between p-4 bg-(--card) border border-(--card-border) rounded-xl hover:border-blue-200 dark:hover:border-blue-800 hover:shadow-md transition-all group/item">
                                <div className="flex items-center gap-4">
                                    <div className="w-10 h-10 rounded-full bg-linear-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-white font-bold shadow-md">
                                        {s.name.charAt(0)}
                                    </div>
                                    <div>
                                        <p className="font-bold text-(--foreground) group-hover/item:text-blue-600 dark:group-hover/item:text-blue-400 transition-colors">{s.name}</p>
                                        <p className="text-sm text-gray-500">{s.email}</p>
                                    </div>
                                </div>
                                <button
                                    onClick={() => handleUnassign("/admin/events/assign-scanner", s.id)}
                                    className="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition-all"
                                    title="Unassign User"
                                >
                                    <Trash className="w-4 h-4" />
                                </button>
                            </div>
                        ))}
                        {scanners.length === 0 && (
                            <div className="text-center py-12 bg-(--background) rounded-2xl border-2 border-dashed border-(--card-border)">
                                <ScanBarcode className="w-12 h-12 text-gray-300 mx-auto mb-3" />
                                <p className="text-gray-500 font-bold opacity-60">No scanners assigned yet.</p>
                                <p className="text-xs text-gray-400 mt-1">Select a user above to get started.</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            {/* Resellers Section */}
            <div className="relative group">
                <div className="absolute -inset-0.5 bg-linear-to-r from-emerald-500 to-teal-500 rounded-2xl opacity-20 group-hover:opacity-30 transition duration-500 blur-sm"></div>
                <div className="relative bg-(--card) p-8 rounded-2xl border border-(--card-border) shadow-xl">
                    <div className="flex justify-between items-center mb-6 border-b border-(--card-border) pb-4">
                        <div>
                            <h3 className="text-xl font-bold text-(--foreground)">Assigned Resellers</h3>
                            <p className="text-sm text-gray-500 mt-1">Set up commission rates and assign resellers.</p>
                        </div>
                        <div className="w-10 h-10 bg-emerald-500/10 rounded-full flex items-center justify-center text-emerald-600">
                            <Store className="w-5 h-5" />
                        </div>
                    </div>

                    <div className="bg-(--background) p-6 rounded-2xl border border-(--card-border) mb-8">
                        <div className="grid grid-cols-1 md:grid-cols-12 gap-4 items-end">
                            <div className="md:col-span-12 lg:col-span-5">
                                <Select
                                    label="Select Reseller"
                                    value={selectedReseller}
                                    onChange={(val) => setSelectedReseller(val)}
                                    className="py-3 rounded-xl border-emerald-200 dark:border-emerald-900/50 focus:border-emerald-500"
                                    placeholder="Choose a partner..."
                                    options={availableResellersList.map(u => ({ label: `${u.name} (${u.email})`, value: String(u.id) }))}
                                />
                            </div>

                            <div className="md:col-span-6 lg:col-span-3">
                                <Select
                                    label="Type"
                                    value={commissionType}
                                    onChange={(val) => setCommissionType(val)}
                                    className="py-3 rounded-xl border-emerald-200 dark:border-emerald-900/50 focus:border-emerald-500"
                                    options={[
                                        { label: "Fixed Amount (Rp)", value: "fixed" },
                                        { label: "Percentage (%)", value: "percent" }
                                    ]}
                                />
                            </div>

                            <div className="md:col-span-6 lg:col-span-2">
                                <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-2 ml-1">Commission</label>
                                <input
                                    type="number"
                                    className="w-full px-4 py-3 rounded-xl border border-(--card-border) focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10 outline-none transition-all bg-(--background) text-(--foreground) font-medium"
                                    value={commissionValue}
                                    min="0"
                                    onChange={e => setCommissionValue(e.target.value)}
                                />
                            </div>

                            <div className="md:col-span-12 lg:col-span-2">
                                <button
                                    onClick={handleAssignReseller}
                                    className="w-full py-3 bg-emerald-600 text-white font-bold rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-600/30 disabled:opacity-70 disabled:shadow-none flex items-center justify-center gap-2 active:scale-95"
                                    disabled={!selectedReseller}
                                >
                                    <Plus className="w-5 h-5" /> Add
                                </button>
                            </div>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {resellers.map(r => (
                            <div key={r.id} className="relative p-5 bg-(--card) border border-(--card-border) rounded-2xl hover:border-emerald-200 hover:shadow-lg transition-all group/card">
                                <div className="flex justify-between items-start mb-3">
                                    <div className="flex items-center gap-3">
                                        <div className="w-10 h-10 rounded-full bg-emerald-500/10 text-emerald-600 flex items-center justify-center font-bold">
                                            {r.name.charAt(0)}
                                        </div>
                                        <div>
                                            <p className="font-bold text-(--foreground) line-clamp-1">{r.name}</p>
                                            <p className="text-xs text-gray-500">{r.email}</p>
                                        </div>
                                    </div>
                                    <button
                                        onClick={() => handleUnassign("/admin/events/assign-reseller", r.id)}
                                        className="text-gray-300 hover:text-red-500 transition-colors p-1"
                                    >
                                        <Trash className="w-4 h-4" />
                                    </button>
                                </div>
                                <div className="flex items-center gap-2 bg-emerald-500/5 p-2 rounded-lg border border-emerald-500/10">
                                    <span className="text-xs font-bold text-emerald-800 dark:text-emerald-400 uppercase tracking-wide">Commission:</span>
                                    <span className="text-sm font-bold text-emerald-600 dark:text-emerald-400">
                                        {r.commission_type === 'fixed' ? 'Rp ' : ''}{new Intl.NumberFormat('id-ID').format(r.commission_value)}{r.commission_type === 'percent' ? '%' : ''}
                                    </span>
                                </div>
                            </div>
                        ))}
                        {resellers.length === 0 && (
                            <div className="col-span-full text-center py-12 bg-(--background) rounded-2xl border-2 border-dashed border-(--card-border)">
                                <Store className="w-12 h-12 text-gray-300 mx-auto mb-3" />
                                <p className="text-gray-500 font-bold opacity-60">No resellers assigned yet.</p>
                                <p className="text-xs text-gray-400 mt-1">Add users above to start tracking commissions.</p>
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}

// --- TAB 3: TICKETS ---
function TicketsTab({ eventId }: { eventId: string }) {
    const [tickets, setTickets] = useState<Ticket[]>([]);
    const [loading, setLoading] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingTicket, setEditingTicket] = useState<Ticket | null>(null);

    const [formData, setFormData] = useState({
        name: "", description: "", price: 0, quota: 0, max_purchase_per_user: 5,
        start_date: "", end_date: "", is_active: true
    });

    const fetchTickets = async () => {
        setLoading(true);
        try {
            const res = await axiosInstance.get(`/admin/tickets?event_id=${eventId}`);
            setTickets(res.data || []);
        } catch (e) { toast.error("Failed to load tickets"); }
        finally { setLoading(false); }
    };

    // eslint-disable-next-line react-hooks/exhaustive-deps
    useEffect(() => { fetchTickets(); }, [eventId]);

    const handleOpenModal = (ticket?: Ticket) => {
        if (ticket) {
            setEditingTicket(ticket);
            setFormData({
                name: ticket.name,
                description: ticket.description || "",
                price: ticket.price,
                quota: ticket.quota,
                max_purchase_per_user: ticket.max_purchase_per_user,
                start_date: ticket.start_date,
                end_date: ticket.end_date,
                is_active: ticket.is_active
            });
        } else {
            setEditingTicket(null);
            setFormData({ name: "", description: "", price: 0, quota: 0, max_purchase_per_user: 5, start_date: "", end_date: "", is_active: true });
        }
        setIsModalOpen(true);
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        try {
            const payload = { ...formData, event_id: Number(eventId) };
            if (editingTicket) {
                await axiosInstance.put(`/admin/tickets?id=${editingTicket.id}`, payload);
                toast.success("Ticket updated");
            } else {
                await axiosInstance.post(`/admin/tickets`, payload);
                toast.success("Ticket created");
            }
            setIsModalOpen(false);
            fetchTickets();
        } catch (error) {
            // eslint-disable-next-line @typescript-eslint/no-explicit-any
            toast.error((error as any).response?.data?.error || "Failed to save ticket");
        }
    };

    const handleDelete = async (id: number) => {
        if (!confirm("Are you sure?")) return;
        try {
            await axiosInstance.delete(`/admin/tickets?id=${id}`);
            toast.success("Ticket deleted");
            fetchTickets();
        } catch { toast.error("Failed to delete ticket"); }
    };

    return (
        <div className="space-y-6 mt-8">
            <div className="flex justify-between items-center">
                <h3 className="text-xl font-bold text-(--foreground)">Ticket Configuration</h3>
                <button onClick={() => handleOpenModal()} className="px-4 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 flex items-center gap-2">
                    <Plus className="w-5 h-5" /> Add Ticket
                </button>
            </div>

            {loading ? <div>Loading tickets...</div> : (
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {tickets.map(t => (
                        <div key={t.id} className="bg-(--card) border border-(--card-border) rounded-xl p-6 hover:shadow-lg transition-all relative group">
                            <div className="flex justify-between items-start mb-4">
                                <div>
                                    <h4 className="font-bold text-lg text-(--foreground)">{t.name}</h4>
                                    <span className={`text-xs px-2 py-1 rounded-full font-bold ${t.is_active ? 'bg-green-500/10 text-green-600' : 'bg-red-500/10 text-red-600'}`}>
                                        {t.is_active ? 'Active' : 'Inactive'}
                                    </span>
                                </div>
                                <div className="flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button onClick={() => handleOpenModal(t)} className="p-2 text-blue-600 hover:bg-blue-500/10 rounded-lg">Edit</button>
                                    <button onClick={() => handleDelete(t.id)} className="p-2 text-red-600 hover:bg-red-500/10 rounded-lg"><Trash className="w-4 h-4" /></button>
                                </div>
                            </div>
                            <div className="space-y-2 text-sm text-gray-500">
                                <div className="flex justify-between"><span>Price:</span> <span className="font-bold text-(--foreground)">Rp {new Intl.NumberFormat('id-ID').format(t.price)}</span></div>
                                <div className="flex justify-between"><span>Quota:</span> <span className="font-bold text-(--foreground) opacity-80">{t.quota}</span></div>
                                <div className="flex justify-between"><span>Max/User:</span> <span className="font-bold text-(--foreground) opacity-80">{t.max_purchase_per_user}</span></div>
                                <div className="border-t border-(--card-border) pt-2 mt-2 text-xs text-gray-400">
                                    {new Date(t.start_date).toLocaleDateString()} - {new Date(t.end_date).toLocaleDateString()}
                                </div>
                            </div>
                        </div>
                    ))}
                    {tickets.length === 0 && <div className="col-span-full text-center py-12 text-gray-500">No tickets configured.</div>}
                </div>
            )}

            {isModalOpen && (
                <div className="fixed inset-0 bg-black/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div className="bg-(--card) border border-(--card-border) rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
                        <div className="p-6 border-b border-(--card-border) flex justify-between items-center bg-(--background) sticky top-0 z-10">
                            <h3 className="font-bold text-xl text-(--foreground)">{editingTicket ? 'Edit Ticket' : 'Create Ticket'}</h3>
                            <button onClick={() => setIsModalOpen(false)} className="text-gray-400 hover:text-(--foreground) transition-colors">Close</button>
                        </div>
                        <form onSubmit={handleSubmit} className="p-6 space-y-4">
                            <div><label className="block text-sm font-bold text-(--foreground) opacity-70 mb-1">Name</label><input required className="w-full border border-(--card-border) rounded-lg px-4 py-2 bg-(--background) text-(--foreground) outline-none focus:border-blue-500" value={formData.name} onChange={e => setFormData({ ...formData, name: e.target.value })} /></div>
                            <div className="grid grid-cols-2 gap-4">
                                <div><label className="block text-sm font-bold text-(--foreground) opacity-70 mb-1">Price (Rp)</label><input type="number" required className="w-full border border-(--card-border) rounded-lg px-4 py-2 bg-(--background) text-(--foreground) outline-none focus:border-blue-500" value={formData.price} onChange={e => setFormData({ ...formData, price: Number(e.target.value) })} /></div>
                                <div><label className="block text-sm font-bold text-(--foreground) opacity-70 mb-1">Quota</label><input type="number" required className="w-full border border-(--card-border) rounded-lg px-4 py-2 bg-(--background) text-(--foreground) outline-none focus:border-blue-500" value={formData.quota} onChange={e => setFormData({ ...formData, quota: Number(e.target.value) })} /></div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div><label className="block text-sm font-bold text-(--foreground) opacity-70 mb-1">Max Per User</label><input type="number" required className="w-full border border-(--card-border) rounded-lg px-4 py-2 bg-(--background) text-(--foreground) outline-none focus:border-blue-500" value={formData.max_purchase_per_user} onChange={e => setFormData({ ...formData, max_purchase_per_user: Number(e.target.value) })} /></div>
                                <div className="flex items-center gap-2 pt-6">
                                    <input type="checkbox" checked={formData.is_active} onChange={e => setFormData({ ...formData, is_active: e.target.checked })} className="w-5 h-5 rounded border-(--card-border) text-blue-600 focus:ring-blue-500 bg-(--background)" />
                                    <label className="font-bold text-(--foreground) opacity-70">Active</label>
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div><DatePicker label="Start Date" value={formData.start_date} onChange={val => setFormData({ ...formData, start_date: val })} /></div>
                                <div><DatePicker label="End Date" value={formData.end_date} onChange={val => setFormData({ ...formData, end_date: val })} /></div>
                            </div>
                            <div>
                                <RichTextEditor label="Description" value={formData.description} onChange={val => setFormData({ ...formData, description: val })} />
                            </div>
                            <div className="pt-4 flex justify-end gap-2">
                                <button type="button" onClick={() => setIsModalOpen(false)} className="px-4 py-2 text-gray-600 font-bold hover:bg-gray-100 rounded-lg">Cancel</button>
                                <button type="submit" className="px-6 py-2 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700">Save Ticket</button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}

// --- MAIN PAGE ---
function EditEventWrapper() {
    const searchParams = useSearchParams();
    const id = searchParams.get("id");
    const [activeTab, setActiveTab] = useState("details");
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const [eventData, setEventData] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    const fetchEvent = async () => {
        if (!id) return;
        try {
            const res = await axiosInstance.get(`/admin/events?id=${id}`);
            setEventData(Array.isArray(res.data) ? res.data[0] : res.data);
        } catch (e) { toast.error("Failed to load event"); }
        finally { setLoading(false); }
    };

    // eslint-disable-next-line react-hooks/exhaustive-deps
    useEffect(() => { fetchEvent(); }, [id]);

    if (!id) return null;
    if (loading) return <div className="flex h-64 justify-center items-center"><Loader2 className="animate-spin" /></div>;

    return (
        <div className="max-w-5xl mx-auto pb-20">
            <div className="flex items-center gap-4 mb-6">
                <Link href="/admin/events" className="p-2 hover:bg-(--card) border border-transparent hover:border-(--card-border) rounded-lg text-gray-500 transition-all"><ChevronLeft className="w-5 h-5" /></Link>
                <div>
                    <h2 className="text-2xl font-bold text-(--foreground)">Edit Event: {eventData?.name}</h2>
                    <p className="text-gray-500 text-sm">Manage details and assignments.</p>
                </div>
            </div>

            <Tabs
                activeKey={activeTab}
                onChange={setActiveTab}
                items={[
                    { key: "details", label: "Event Details" },
                    { key: "tickets", label: "Tickets" },
                    { key: "assignments", label: "Assignments" },
                ]}
            />

            {activeTab === "details" && <EventDetailsTab id={id} initialData={eventData} refresh={fetchEvent} />}
            {activeTab === "tickets" && <TicketsTab eventId={id} />}
            {activeTab === "assignments" && <AssignmentsTab eventId={id} />}
        </div>
    );
}

export default function EditEvent() {
    return (
        <Suspense fallback={<div>Loading...</div>}>
            <EditEventWrapper />
        </Suspense>
    );
}
