"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import axiosInstance from "@/lib/axios";
import { Plus, Search, Calendar, MapPin, MoreVertical, Edit, Trash, ExternalLink } from "lucide-react";
import { toast } from "react-hot-toast";

interface Event {
    id: number;
    name: string;
    category: string;
    start_date: string;
    location: string;
    status: string;
    slug: string;
    thumbnail_path: string | null;
}

export default function OrganizerEventListContent() {
    const [events, setEvents] = useState<Event[]>([]);
    const [loading, setLoading] = useState(true);
    const [searchTerm, setSearchTerm] = useState("");

    const fetchEvents = async () => {
        try {
            // Using the organizer endpoint
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

    const filteredEvents = events.filter(event =>
        event.name.toLowerCase().includes(searchTerm.toLowerCase())
    );

    if (loading) {
        return (
            <div className="flex items-center justify-center h-64">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>
        );
    }

    return (
        <div className="space-y-6">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 className="text-2xl font-bold text-(--foreground)">My Events</h1>
                    <p className="text-gray-500 text-sm mt-1">Manage and monitor your hosted events.</p>
                </div>
            </div>

            <div className="flex items-center gap-4 bg-(--card) p-4 rounded-2xl border border-(--card-border) shadow-sm">
                <Search className="w-5 h-5 text-gray-400" />
                <input
                    type="text"
                    placeholder="Search your events..."
                    className="flex-1 bg-transparent border-none outline-none text-(--foreground) placeholder:text-gray-500"
                    value={searchTerm}
                    onChange={(e) => setSearchTerm(e.target.value)}
                />
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {filteredEvents.map((event) => (
                    <div key={event.id} className="bg-(--card) border border-(--card-border) rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-shadow group">
                        <div className="aspect-video relative bg-gray-100 flex items-center justify-center overflow-hidden">
                            {event.thumbnail_path ? (
                                <img
                                    src={`${process.env.NEXT_PUBLIC_STORAGE_URL}/${event.thumbnail_path}`}
                                    alt={event.name}
                                    className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                />
                            ) : (
                                <Calendar className="w-12 h-12 text-gray-300" />
                            )}
                            <div className="absolute top-4 right-4">
                                <span className={cn(
                                    "px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider shadow-sm",
                                    event.status === 'active' ? 'bg-emerald-500 text-white' : 'bg-gray-500 text-white'
                                )}>
                                    {event.status}
                                </span>
                            </div>
                        </div>

                        <div className="p-5 space-y-4">
                            <div>
                                <h3 className="font-bold text-lg text-(--foreground) line-clamp-1 group-hover:text-primary transition-colors">
                                    {event.name}
                                </h3>
                                <div className="flex items-center gap-2 mt-2 text-gray-500 text-xs">
                                    <MapPin className="w-3.5 h-3.5" />
                                    <span>{event.location}</span>
                                </div>
                            </div>

                            <div className="flex items-center justify-between pt-4 border-t border-(--card-border)">
                                <div className="flex items-center gap-3">
                                    <Link
                                        href={`/organizer/events/edit?id=${event.id}`}
                                        className="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-gray-600 dark:text-gray-400 hover:text-primary hover:bg-primary/10 transition-colors"
                                        title="Edit Event"
                                    >
                                        <Edit className="w-4 h-4" />
                                    </Link>
                                    <Link
                                        href={`/events/${event.slug}`}
                                        target="_blank"
                                        className="p-2 bg-gray-100 dark:bg-gray-800 rounded-lg text-gray-600 dark:text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                        title="View Public Page"
                                    >
                                        <ExternalLink className="w-4 h-4" />
                                    </Link>
                                </div>
                                <Link
                                    href={`/organizer/events/report/${event.id}`}
                                    className="text-[10px] font-black uppercase tracking-wider text-primary hover:underline"
                                >
                                    View Report
                                </Link>
                            </div>
                        </div>
                    </div>
                ))}
            </div>

            {filteredEvents.length === 0 && (
                <div className="text-center py-20 bg-(--card) rounded-3xl border border-(--card-border) border-dashed">
                    <Calendar className="w-16 h-16 text-gray-300 mx-auto mb-4" />
                    <h3 className="text-xl font-bold text-(--foreground)">No events found</h3>
                    <p className="text-gray-500 mt-2">You haven&apos;t created any events yet or none match your search.</p>
                </div>
            )}
        </div>
    );
}

function cn(...classes: string[]) {
    return classes.filter(Boolean).join(' ');
}
