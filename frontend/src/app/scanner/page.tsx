"use client";

import { useEffect, useState } from "react";
import axiosInstance from "@/lib/axios";
import { Calendar, MapPin, ScanBarcode, AlertCircle } from "lucide-react";
import Link from "next/link";

interface AssignedEvent {
    id: number;
    name: string;
    banner_path: string | null;
    start_date: string;
    end_date: string;
    location: string | null;
    city: string | null;
    status: string;
}

export default function ScannerDashboard() {
    const [events, setEvents] = useState<AssignedEvent[]>([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState<string | null>(null);

    useEffect(() => {
        axiosInstance.get("/scanner/events")
            .then((res) => setEvents(res.data ?? []))
            .catch(() => setError("Gagal memuat daftar event"))
            .finally(() => setLoading(false));
    }, []);

    if (loading) {
        return (
            <div className="flex items-center justify-center min-h-[60vh]">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary" />
            </div>
        );
    }

    return (
        <div className="p-6 max-w-3xl mx-auto">
            <div className="mb-6">
                <h1 className="text-2xl font-bold text-white">Event Saya</h1>
                <p className="text-gray-400 text-sm mt-1">Pilih event untuk mulai scan tiket</p>
            </div>

            {error && (
                <div className="flex items-center gap-2 bg-red-900/40 border border-red-700 text-red-300 rounded-xl p-4 mb-6">
                    <AlertCircle className="w-5 h-5 shrink-0" />
                    <span>{error}</span>
                </div>
            )}

            {!error && events.length === 0 && (
                <div className="text-center py-20 text-gray-500">
                    <ScanBarcode className="w-12 h-12 mx-auto mb-3 opacity-40" />
                    <p className="text-lg font-medium">Belum ada event yang ditugaskan</p>
                    <p className="text-sm mt-1">Hubungi admin untuk mendapatkan penugasan event</p>
                </div>
            )}

            <div className="space-y-4">
                {events.map((event) => (
                    <div key={event.id} className="bg-gray-900 border border-gray-800 rounded-2xl overflow-hidden flex gap-4 p-4 hover:border-gray-700 transition-colors">
                        {/* Banner thumbnail */}
                        {event.banner_path ? (
                            <img
                                src={`/${event.banner_path}`}
                                alt={event.name}
                                className="w-20 h-20 object-cover rounded-xl shrink-0"
                            />
                        ) : (
                            <div className="w-20 h-20 bg-gray-800 rounded-xl shrink-0 flex items-center justify-center">
                                <Calendar className="w-7 h-7 text-gray-600" />
                            </div>
                        )}

                        {/* Info */}
                        <div className="flex-1 min-w-0">
                            <h2 className="font-semibold text-white truncate">{event.name}</h2>
                            <div className="flex items-center gap-1.5 text-gray-400 text-sm mt-1">
                                <Calendar className="w-3.5 h-3.5 shrink-0" />
                                <span>{new Date(event.start_date).toLocaleDateString("id-ID", { day: "numeric", month: "long", year: "numeric" })}</span>
                            </div>
                            {(event.city || event.location) && (
                                <div className="flex items-center gap-1.5 text-gray-400 text-sm mt-0.5">
                                    <MapPin className="w-3.5 h-3.5 shrink-0" />
                                    <span className="truncate">{event.city || event.location}</span>
                                </div>
                            )}
                            <span className={`inline-block mt-2 text-xs px-2 py-0.5 rounded-full font-medium ${event.status === "published" ? "bg-green-900/50 text-green-400" : "bg-yellow-900/50 text-yellow-400"}`}>
                                {event.status}
                            </span>
                        </div>

                        {/* Action */}
                        <div className="flex items-center shrink-0">
                            <Link
                                href={`/scanner/scan/${event.id}`}
                                className="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-4 py-2 rounded-xl text-sm font-medium transition-colors"
                            >
                                <ScanBarcode className="w-4 h-4" />
                                Scan
                            </Link>
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );
}
