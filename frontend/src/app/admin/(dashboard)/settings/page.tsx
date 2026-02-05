"use client";

import { useEffect, useState } from "react";
import axiosInstance from "@/lib/axios";
import { toast } from "react-hot-toast";
import { Save, Loader2 } from "lucide-react";

export default function SettingsPage() {
    const [settings, setSettings] = useState<Record<string, string>>({});
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);

    useEffect(() => {
        axiosInstance.get("/admin/settings")
            .then(res => setSettings(res.data || {}))
            .catch(() => toast.error("Failed to load settings"))
            .finally(() => setLoading(false));
    }, []);

    const handleChange = (key: string, value: string) => {
        setSettings(prev => ({ ...prev, [key]: value }));
    };

    const handleSave = async (e: React.FormEvent) => {
        e.preventDefault();
        setSaving(true);
        try {
            await axiosInstance.put("/admin/settings", settings); // Backend expects Map<string, string>
            toast.success("Settings saved successfully!");
        } catch (e) { toast.error("Failed to save settings"); }
        finally { setSaving(false); }
    };

    if (loading) return <div>Loading...</div>;

    return (
        <div className="max-w-2xl">
            <h2 className="text-2xl font-bold text-(--foreground) mb-2">General Settings</h2>
            <p className="text-gray-500 text-sm mb-6">Configure global application settings.</p>

            <form onSubmit={handleSave} className="bg-(--card) p-6 rounded-xl border border-(--card-border) shadow-sm space-y-6">

                <div>
                    <h3 className="font-bold text-(--foreground) mb-4 pb-2 border-b border-(--card-border)">Site Identity</h3>
                    <div className="space-y-4">
                        <div>
                            <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Site Name</label>
                            <input type="text" className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-(--background) text-(--foreground)"
                                value={settings["site_name"] || ""}
                                onChange={e => handleChange("site_name", e.target.value)}
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Support Email</label>
                            <input type="email" className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-(--background) text-(--foreground)"
                                value={settings["support_email"] || ""}
                                onChange={e => handleChange("support_email", e.target.value)}
                            />
                        </div>
                    </div>
                </div>

                <div>
                    <h3 className="font-bold text-(--foreground) mb-4 pb-2 border-b border-(--card-border)">Social Media</h3>
                    <div className="space-y-4">
                        <div>
                            <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Instagram URL</label>
                            <input type="url" className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-(--background) text-(--foreground)"
                                value={settings["instagram_url"] || ""}
                                onChange={e => handleChange("instagram_url", e.target.value)}
                            />
                        </div>
                        <div>
                            <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Facebook URL</label>
                            <input type="url" className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all bg-(--background) text-(--foreground)"
                                value={settings["facebook_url"] || ""}
                                onChange={e => handleChange("facebook_url", e.target.value)}
                            />
                        </div>
                    </div>
                </div>

                <div className="pt-4 flex justify-end">
                    <button type="submit" disabled={saving} className="px-6 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-sm shadow-blue-600/20 disabled:opacity-70 flex items-center justify-center">
                        {saving ? <Loader2 className="w-5 h-5 animate-spin" /> : <Save className="w-5 h-5 mr-2" />}
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    );
}
