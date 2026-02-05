"use client";

import { useEffect, useState } from "react";
import Image from "next/image";
import Link from "next/link";
import { Calendar, MapPin, Ticket } from "lucide-react";
import api from "@/lib/axios";

interface Event {
    id: number;
    slug: string;
    name: string;
    description: string;
    start_date: string;
    location: string;
    thumbnail_path: string | null;
    status: 'draft' | 'published';
}

export default function EventList() {
    const [events, setEvents] = useState<Event[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchEvents = async () => {
            try {
                const res = await api.get("/events");
                setEvents(res.data);
            } catch (error) {
                console.error("Failed to fetch events", error);
            } finally {
                setLoading(false);
            }
        };

        fetchEvents();
    }, []);

    if (loading) {
        return (
            <div className="flex justify-center py-20">
                <div className="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
            </div>
        );
    }

    if (events.length === 0) {
        return (
            <div className="text-center py-20">
                <h3 className="text-xl font-bold text-gray-700">No events found</h3>
                <p className="text-gray-500">Check back later for upcoming events!</p>
            </div>
        );
    }

    return (
        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            {events.map((event) => (
                <Link key={event.id} href={`/events/${event.slug}`} className="group bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden flex flex-col h-full">
                    <div className="relative h-48 w-full overflow-hidden bg-gray-100">
                        {event.thumbnail_path ? (
                            <Image
                                src={`http://localhost:8080${event.thumbnail_path}`}
                                alt={event.name}
                                fill
                                className="object-cover group-hover:scale-105 transition-transform duration-500"
                            />
                        ) : (
                            <div className="flex items-center justify-center h-full text-gray-300">
                                <Ticket className="w-12 h-12" />
                            </div>
                        )}
                        <div className="absolute top-2 right-2 bg-white/90 backdrop-blur-sm px-2 py-1 rounded-lg text-xs font-bold text-blue-600 shadow-sm">
                            Upcoming
                        </div>
                    </div>

                    <div className="p-5 flex-1 flex flex-col">
                        <h3 className="font-bold text-lg text-gray-900 group-hover:text-blue-600 transition-colors line-clamp-2 mb-2">
                            {event.name}
                        </h3>

                        <div className="space-y-2 mt-auto">
                            <div className="flex items-center gap-2 text-sm text-gray-500">
                                <Calendar className="w-4 h-4 text-blue-500" />
                                <span>{new Date(event.start_date).toLocaleDateString(undefined, { day: 'numeric', month: 'short', year: 'numeric' })}</span>
                            </div>
                            <div className="flex items-center gap-2 text-sm text-gray-500">
                                <MapPin className="w-4 h-4 text-red-500" />
                                <span className="line-clamp-1">{event.location || "TBA"}</span>
                            </div>
                        </div>
                    </div>
                </Link>
            ))}
        </div>
    );
}
