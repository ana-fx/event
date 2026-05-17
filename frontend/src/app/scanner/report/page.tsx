"use client";

import { useEffect, useState, useMemo } from "react";
import axiosInstance from "@/lib/axios";
import { ClipboardList, Calendar, ChevronDown, User, Ticket, Clock, AlertCircle } from "lucide-react";

interface AssignedEvent {
    id: number;
    name: string;
}

interface ReportRow {
    id: number;
    code: string;
    name: string;
    email: string;
    quantity: number;
    redeemed_at: string;
    ticket_name: string;
    event_name: string;
}

export default function ScanReportPage() {
    const [events, setEvents] = useState<AssignedEvent[]>([]);
    const [report, setReport] = useState<ReportRow[]>([]);
    const [loading, setLoading] = useState(true);
    const [filterEventId, setFilterEventId] = useState<string>("all");

    useEffect(() => {
        Promise.all([
            axiosInstance.get("/scanner/events"),
            axiosInstance.get("/scanner/report"),
        ]).then(([evRes, repRes]) => {
            setEvents(evRes.data ?? []);
            setReport(repRes.data ?? []);
        }).finally(() => setLoading(false));
    }, []);

    const filtered = useMemo(() => {
        if (filterEventId === "all") return report;
        return report.filter(r =>
            events.find(e => e.id === Number(filterEventId))?.name === r.event_name
        );
    }, [report, filterEventId, events]);

    const formatTime = (iso: string) => {
        if (!iso) return "-";
        return new Date(iso).toLocaleString("id-ID", {
            day: "2-digit", month: "short", year: "numeric",
            hour: "2-digit", minute: "2-digit"
        });
    };

    return (
        <div className="p-6 max-w-3xl mx-auto">
            {/* Header */}
            <div className="mb-6">
                <div className="flex items-center gap-2 mb-1">
                    <ClipboardList className="w-5 h-5 text-primary" />
                    <h1 className="text-xl font-bold text-white">Scan Report</h1>
                </div>
                <p className="text-gray-400 text-sm">Riwayat tiket yang sudah kamu scan</p>
            </div>

            {/* Filter */}
            {events.length > 0 && (
                <div className="relative mb-6">
                    <Calendar className="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" />
                    <select
                        value={filterEventId}
                        onChange={e => setFilterEventId(e.target.value)}
                        className="w-full bg-gray-900 border border-gray-700 text-white text-sm rounded-xl pl-9 pr-10 py-3 outline-none focus:border-primary appearance-none"
                    >
                        <option value="all">Semua Event</option>
                        {events.map(ev => (
                            <option key={ev.id} value={ev.id}>{ev.name}</option>
                        ))}
                    </select>
                    <ChevronDown className="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-500 pointer-events-none" />
                </div>
            )}

            {/* Stats */}
            {!loading && filtered.length > 0 && (
                <div className="grid grid-cols-2 gap-3 mb-6">
                    <div className="bg-gray-900 border border-gray-800 rounded-2xl p-4">
                        <p className="text-xs text-gray-500 mb-1">Total Scan</p>
                        <p className="text-2xl font-bold text-white">{filtered.length}</p>
                        <p className="text-xs text-gray-500 mt-0.5">transaksi</p>
                    </div>
                    <div className="bg-gray-900 border border-gray-800 rounded-2xl p-4">
                        <p className="text-xs text-gray-500 mb-1">Total Tiket</p>
                        <p className="text-2xl font-bold text-primary">
                            {filtered.reduce((acc, r) => acc + (r.quantity || 1), 0)}
                        </p>
                        <p className="text-xs text-gray-500 mt-0.5">tiket masuk</p>
                    </div>
                </div>
            )}

            {/* Loading */}
            {loading && (
                <div className="flex items-center justify-center py-20">
                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary" />
                </div>
            )}

            {/* Empty */}
            {!loading && filtered.length === 0 && (
                <div className="text-center py-20 text-gray-500">
                    <AlertCircle className="w-10 h-10 mx-auto mb-3 opacity-40" />
                    <p className="font-medium">Belum ada tiket yang di-scan</p>
                    <p className="text-sm mt-1">Tiket yang kamu scan akan muncul di sini</p>
                </div>
            )}

            {/* List */}
            {!loading && filtered.length > 0 && (
                <div className="space-y-3">
                    {filtered.map((row, i) => (
                        <div key={row.id} className="bg-gray-900 border border-gray-800 rounded-2xl p-4 flex gap-4 items-start">
                            <div className="w-8 h-8 rounded-full bg-primary/10 text-primary text-xs font-bold flex items-center justify-center shrink-0 mt-0.5">
                                {i + 1}
                            </div>
                            <div className="flex-1 min-w-0 space-y-1.5">
                                <div className="flex items-center justify-between gap-2">
                                    <p className="font-semibold text-white text-sm truncate">{row.name}</p>
                                    <span className="text-xs bg-green-900/50 text-green-400 px-2 py-0.5 rounded-full font-medium shrink-0">Masuk</span>
                                </div>
                                {filterEventId === "all" && (
                                    <p className="text-xs text-primary/80 font-medium truncate">{row.event_name}</p>
                                )}
                                <div className="flex items-center gap-1.5 text-gray-500 text-xs">
                                    <Ticket className="w-3 h-3 shrink-0" />
                                    <span className="truncate">{row.ticket_name || "—"}</span>
                                    <span className="text-gray-700">·</span>
                                    <span>{row.quantity || 1} tiket</span>
                                </div>
                                <div className="flex items-center gap-1.5 text-gray-500 text-xs">
                                    <User className="w-3 h-3 shrink-0" />
                                    <span className="truncate">{row.email}</span>
                                </div>
                                <div className="flex items-center gap-1.5 text-gray-600 text-xs">
                                    <Clock className="w-3 h-3 shrink-0" />
                                    <span>{formatTime(row.redeemed_at)}</span>
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            )}
        </div>
    );
}
