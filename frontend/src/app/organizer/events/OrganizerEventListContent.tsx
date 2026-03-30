"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import axiosInstance from "@/lib/axios";
import { Search, Plus, Calendar, MapPin, Edit2, Trash2, BarChart3 } from "lucide-react";
import { toast } from "react-hot-toast";
import { getImageUrl } from "@/lib/utils";

interface Event {
    id: number;
    name: string;
    category: string;
    start_date: string;
    end_date: string;
    location: string;
    status: string;
    slug: string;
    thumbnail_path: string | null;
}

export default function OrganizerEventListContent() {
    const [events, setEvents] = useState<Event[]>([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState("");

    const fetchEvents = async () => {
        try {
            const res = await axiosInstance.get("/organizer/events");
            setEvents(res.data || []);
        } catch (error) {
            console.error("Failed to fetch events", error);
            toast.error("Failed to load events");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchEvents();
    }, []);

    const handleDelete = async (id: number) => {
        if (!confirm("Are you sure you want to delete this event? This action cannot be undone.")) return;
        try {
            await axiosInstance.delete(`/organizer/events?id=${id}`);
            toast.success("Event deleted successfully");
            fetchEvents();
        } catch {
            toast.error("Failed to delete event");
        }
    };

    const filteredEvents = events.filter(e =>
        e.name.toLowerCase().includes(search.toLowerCase()) ||
        (e.location ?? "").toLowerCase().includes(search.toLowerCase())
    );

    return (
        <div className="space-y-6">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-3xl font-bold text-(--foreground)">My Events</h1>
                    <p className="text-gray-500 text-sm">Manage and monitor your hosted events.</p>
                </div>
                <Link
                    href="/organizer/events/create"
                    className="flex items-center gap-2 px-4 py-2.5 bg-primary text-white font-medium rounded-lg hover:bg-primary/90 transition-colors shadow-sm shadow-primary/20"
                >
                    <Plus className="w-4 h-4" />
                    Create Event
                </Link>
            </div>

            <div className="bg-(--card) p-6 rounded-2xl border border-(--card-border) shadow-sm space-y-4 mb-8">
                <div className="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div className="md:col-span-8 relative group">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors" />
                        <input
                            type="text"
                            placeholder="Search events by name or location..."
                            className="w-full pl-11 pr-4 py-3 rounded-xl border border-(--card-border) focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-(--background) text-(--foreground)"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                        />
                    </div>
                </div>
            </div>

            <div className="bg-(--card) rounded-2xl border border-(--card-border) shadow-sm overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left">
                        <thead>
                            <tr className="bg-(--background) border-b border-(--card-border) text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                <th className="px-6 py-4">Event</th>
                                <th className="px-6 py-4">Status</th>
                                <th className="px-6 py-4">Date & Time</th>
                                <th className="px-6 py-4 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-(--card-border)">
                            {loading ? (
                                <tr>
                                    <td colSpan={4} className="px-6 py-8 text-center text-gray-500">
                                        <div className="flex justify-center items-center gap-2">
                                            <div className="animate-spin rounded-full h-4 w-4 border-b-2 border-primary"></div>
                                            Loading events...
                                        </div>
                                    </td>
                                </tr>
                            ) : filteredEvents.length === 0 ? (
                                <tr>
                                    <td colSpan={4} className="px-6 py-8 text-center text-gray-500">
                                        No events found.
                                    </td>
                                </tr>
                            ) : (
                                filteredEvents.map((event) => (
                                    <tr key={event.id} className="hover:bg-primary/5 transition-colors group">
                                        <td className="px-6 py-4 text-(--foreground)">
                                            <div className="flex items-center gap-4">
                                                <div className="w-12 h-12 rounded-lg bg-(--background) overflow-hidden shrink-0 border border-(--card-border)">
                                                    {event.thumbnail_path ? (
                                                        <img src={getImageUrl(event.thumbnail_path)} alt="" className="w-full h-full object-cover" />
                                                    ) : (
                                                        <div className="w-full h-full flex items-center justify-center text-gray-400"><Calendar className="w-5 h-5" /></div>
                                                    )}
                                                </div>
                                                <div>
                                                    <div className="font-bold text-(--foreground) line-clamp-1">{event.name}</div>
                                                    <div className="flex items-center gap-1 text-xs text-gray-500 mt-0.5">
                                                        <MapPin className="w-3 h-3" />
                                                        <span className="line-clamp-1">{event.location}</span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="bg-(--background) px-3 py-1.5 rounded-lg border border-(--card-border) inline-flex flex-col">
                                                <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold ${event.status === 'active'
                                                    ? 'bg-green-600/10 text-green-600'
                                                    : 'bg-gray-600/10 text-gray-600'
                                                    }`}>
                                                    {event.status.charAt(0).toUpperCase() + event.status.slice(1)}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4">
                                            <div className="flex flex-col text-sm text-gray-500">
                                                <span className="flex items-center gap-1.5">
                                                    <Calendar className="w-3.5 h-3.5 text-gray-400" />
                                                    {new Date(event.start_date).toLocaleDateString()}
                                                </span>
                                                <span className="text-xs text-gray-400 pl-5">
                                                    {new Date(event.start_date).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}
                                                </span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-4 text-right">
                                            <div className="flex items-center justify-end gap-2">
                                                <Link
                                                    href={`/organizer/events/report/${event.id}`}
                                                    className="p-2 text-gray-400 hover:text-green-600 hover:bg-green-600/10 rounded-lg transition-colors"
                                                    title="Report"
                                                >
                                                    <BarChart3 className="w-4 h-4" />
                                                </Link>
                                                <Link
                                                    href={`/organizer/events/edit?id=${event.id}`}
                                                    className="p-2 text-gray-400 hover:text-primary hover:bg-primary/10 rounded-lg transition-colors"
                                                    title="Edit"
                                                >
                                                    <Edit2 className="w-4 h-4" />
                                                </Link>
                                                <button
                                                    onClick={() => handleDelete(event.id)}
                                                    className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-600/10 rounded-lg transition-colors"
                                                    title="Delete"
                                                >
                                                    <Trash2 className="w-4 h-4" />
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}
