"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import Image from "next/image";
import axiosInstance from "@/lib/axios";
import { Plus, Search, Edit2, Trash2, Store, MapPin, Phone, Mail } from "lucide-react";
import { toast } from "react-hot-toast";

interface Organizer {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
    username: { String: string; Valid: boolean };
    organizer_name: { String: string; Valid: boolean };
    phone: { String: string; Valid: boolean };
    city: { String: string; Valid: boolean };
    profile_photo_path: { String: string; Valid: boolean };
}

export default function OrganizerListContent() {
    const [organizers, setOrganizers] = useState<Organizer[]>([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState("");

    const fetchOrganizers = async () => {
        try {
            const res = await axiosInstance.get("/admin/organizers");
            setOrganizers(res.data || []);
        } catch (error) {
            toast.error("Failed to load organizers");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchOrganizers();
    }, []);

    const handleDelete = async (id: number) => {
        if (!confirm("Are you sure? This will permanently delete the organizer.")) return;
        try {
            await axiosInstance.delete(`/admin/organizers?id=${id}`);
            toast.success("Organizer deleted");
            fetchOrganizers();
        } catch (error) {
            toast.error("Failed to delete organizer");
        }
    };

    const filteredOrganizers = organizers.filter(org => {
        const matchesSearch =
            org.name.toLowerCase().includes(search.toLowerCase()) ||
            org.email.toLowerCase().includes(search.toLowerCase()) ||
            (org.organizer_name.Valid && org.organizer_name.String.toLowerCase().includes(search.toLowerCase()));
        return matchesSearch;
    });

    return (
        <div className="space-y-6">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 className="text-2xl font-bold text-(--foreground)">Organizers</h2>
                    <p className="text-gray-500 text-sm">Manage event organizers and their profiles.</p>
                </div>
                <Link
                    href="/admin/organizers/new"
                    className="px-4 py-2.5 bg-primary text-white font-bold rounded-lg hover:bg-primary transition-colors shadow-sm shadow-primary/20 flex items-center justify-center text-sm"
                >
                    <Plus className="w-4 h-4 mr-2" />
                    New Organizer
                </Link>
            </div>

            <div className="bg-(--card) p-4 rounded-xl border border-(--card-border) shadow-sm">
                <div className="relative">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                    <input
                        type="text"
                        placeholder="Search by name, email, or organizer name..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        className="w-full pl-10 pr-4 py-2.5 rounded-lg border border-(--card-border) bg-(--background) text-(--foreground) focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all text-sm"
                    />
                </div>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {loading ? (
                    <div className="col-span-full py-20 text-center text-gray-400 italic">Loading organizers...</div>
                ) : filteredOrganizers.length === 0 ? (
                    <div className="col-span-full py-20 text-center text-gray-400 italic bg-(--card) rounded-xl border border-dashed border-(--card-border)">
                        No organizers found.
                    </div>
                ) : (
                    filteredOrganizers.map(org => (
                        <div key={org.id} className="bg-(--card) rounded-2xl border border-(--card-border) p-6 shadow-sm hover:shadow-md transition-all group flex flex-col items-center text-center relative overflow-hidden">
                            <div className="absolute top-0 left-0 w-full h-1.5 bg-primary" />

                            <div className="relative mb-4">
                                <div className="w-24 h-24 rounded-2xl bg-gray-100 dark:bg-gray-800 overflow-hidden border-2 border-white shadow-sm">
                                    {org.profile_photo_path.Valid ? (
                                        <Image
                                            src={`${process.env.NEXT_PUBLIC_API_URL?.replace('/api', '')}${org.profile_photo_path.String}`}
                                            alt={org.name}
                                            width={96}
                                            height={96}
                                            className="w-full h-full object-cover"
                                        />
                                    ) : (
                                        <div className="w-full h-full flex items-center justify-center text-primary bg-primary/5">
                                            <Store className="w-10 h-10" />
                                        </div>
                                    )}
                                </div>
                                <div className={`absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white shadow-sm ${org.is_active ? 'bg-green-500' : 'bg-red-500'}`} />
                            </div>

                            <h3 className="text-lg font-bold text-(--foreground) mb-1 truncate w-full">
                                {org.organizer_name.Valid ? org.organizer_name.String : org.name}
                            </h3>
                            <p className="text-xs text-gray-500 mb-4">{org.email}</p>

                            <div className="w-full space-y-2 mb-6 text-sm text-gray-500">
                                <div className="flex items-center justify-center gap-2">
                                    <MapPin className="w-4 h-4 text-primary/60" />
                                    <span>{org.city.Valid ? org.city.String : 'City Not Set'}</span>
                                </div>
                                <div className="flex items-center justify-center gap-2">
                                    <Phone className="w-4 h-4 text-primary/60" />
                                    <span>{org.phone.Valid ? org.phone.String : 'No Phone'}</span>
                                </div>
                            </div>

                            <div className="flex items-center gap-2 w-full mt-auto">
                                <Link
                                    href={`/admin/organizers/edit?id=${org.id}`}
                                    className="flex-1 flex items-center justify-center gap-2 px-4 py-2 bg-gray-100 dark:bg-gray-800 text-(--foreground) hover:bg-primary hover:text-white rounded-xl transition-all font-bold text-xs"
                                >
                                    <Edit2 className="w-3.5 h-3.5" />
                                    Edit
                                </Link>
                                <button
                                    onClick={() => handleDelete(org.id)}
                                    className="px-4 py-2 bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white rounded-xl transition-all hover:shadow-lg hover:shadow-red-500/20"
                                >
                                    <Trash2 className="w-3.5 h-3.5" />
                                </button>
                            </div>
                        </div>
                    ))
                )}
            </div>
        </div>
    );
}
