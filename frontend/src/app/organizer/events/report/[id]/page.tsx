"use client";

import { useEffect, useState, use } from "react";
import axiosInstance from "@/lib/axios";
import { toast } from "react-hot-toast";
import { ArrowLeft, Download, BarChart3, Ticket, TrendingUp, ShieldCheck, Wallet } from "lucide-react";
import Link from "next/link";

interface TicketReport {
    ticket_id: number;
    name: string;
    price: number;
    is_active: boolean;
    volume: number;
    stock: number;
    total_quota: number;
    revenue: number;
    saldo: number;
    org_tax: number;
    handling_fee: number;
    platform_rev: number;
    service_fee: number;
}

export default function OrganizerEventReportPage({ params }: { params: Promise<{ id: string }> }) {
    const { id } = use(params);
    const [reports, setReports] = useState<TicketReport[]>([]);
    const [loading, setLoading] = useState(true);
    const [eventName, setEventName] = useState("");

    const fetchReport = async () => {
        try {
            // Fetch event name first (using organizer endpoint to ensure access)
            const eventRes = await axiosInstance.get(`/organizer/events?id=${id}`);
            const data = Array.isArray(eventRes.data) ? eventRes.data[0] : eventRes.data;
            setEventName(data?.name || "Event Report");

            // Fetch ticket report
            const res = await axiosInstance.get(`/organizer/reports/tickets?event_id=${id}`);
            setReports(res.data || []);
        } catch (error) {
            toast.error("Failed to load report data");
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchReport();
    }, [id]);

    const formatIDR = (price: number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(price);
    };

    const totals = reports.reduce((acc, curr) => ({
        volume: acc.volume + curr.volume,
        revenue: acc.revenue + curr.revenue,
        saldo: acc.saldo + curr.saldo,
        orgTax: acc.orgTax + curr.org_tax,
        handlingFee: acc.handlingFee + curr.handling_fee,
        platformRev: acc.platformRev + curr.platform_rev,
        serviceFee: acc.serviceFee + curr.service_fee,
    }), { volume: 0, revenue: 0, saldo: 0, orgTax: 0, handlingFee: 0, platformRev: 0, serviceFee: 0 });

    return (
        <div className="space-y-6 animate-fade-in pb-10 p-4 md:p-8">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div className="flex items-center gap-4">
                    <Link
                        href="/organizer/events"
                        className="w-10 h-10 rounded-xl bg-(--card) border border-(--card-border) flex items-center justify-center text-gray-400 hover:text-primary hover:border-primary/50 transition-all shadow-sm"
                    >
                        <ArrowLeft className="w-5 h-5" />
                    </Link>
                    <div>
                        <h1 className="text-3xl font-black text-(--foreground) tracking-tight">Sales Report</h1>
                        <p className="text-gray-500 text-sm font-medium flex items-center gap-2">
                            <BarChart3 className="w-4 h-4 text-primary" />
                            {eventName || "Loading Event..."}
                        </p>
                    </div>
                </div>
                <button
                    onClick={() => toast.success("Export functionality coming soon!")}
                    className="flex items-center gap-2 px-6 py-3 bg-primary text-white font-black rounded-xl hover:bg-primary/90 transition-all shadow-xl shadow-primary/20 active:scale-95 text-[10px] uppercase tracking-[0.2em]"
                >
                    <Download className="w-4 h-4" />
                    Download CSV
                </button>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <StatCard label="Total Tickets" value={totals.volume.toString()} icon={<Ticket className="w-5 h-5" />} color="text-primary" bg="bg-primary/10" />
                <StatCard label="Gross Revenue" value={formatIDR(totals.revenue)} icon={<TrendingUp className="w-5 h-5" />} color="text-primary" bg="bg-primary/10" />
                <StatCard label="Net Saldo" value={formatIDR(totals.saldo)} icon={<Wallet className="w-5 h-5" />} color="text-emerald-500" bg="bg-emerald-500/10" />
                <StatCard label="Fees & Taxes" value={formatIDR(totals.revenue - totals.saldo)} icon={<ShieldCheck className="w-5 h-5" />} color="text-amber-500" bg="bg-amber-500/10" />
            </div>

            <div className="bg-(--card) rounded-2xl border border-(--card-border) shadow-xl overflow-hidden">
                <div className="overflow-x-auto">
                    <table className="w-full text-left min-w-[1000px]">
                        <thead>
                            <tr className="bg-(--background) border-b border-(--card-border) text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                <th className="px-6 py-5 w-16 text-center">No</th>
                                <th className="px-6 py-5">Ticket Type</th>
                                <th className="px-6 py-5 text-center">Status</th>
                                <th className="px-6 py-5 text-center">Sold</th>
                                <th className="px-6 py-5 text-center">Stock</th>
                                <th className="px-6 py-5 text-right">Gross</th>
                                <th className="px-6 py-5 text-right text-emerald-600">Net Saldo</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-(--card-border)">
                            {loading ? (
                                <tr>
                                    <td colSpan={7} className="px-6 py-20 text-center">
                                        <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary mx-auto"></div>
                                    </td>
                                </tr>
                            ) : reports.length === 0 ? (
                                <tr><td colSpan={7} className="px-6 py-20 text-center text-gray-400">No data found.</td></tr>
                            ) : (
                                reports.map((r, i) => (
                                    <tr key={r.ticket_id} className="hover:bg-primary/2 transition-colors">
                                        <td className="px-6 py-6 text-center text-gray-300 font-black text-sm">{i + 1}</td>
                                        <td className="px-6 py-6 font-black">
                                            <div className="flex flex-col">
                                                <span className="text-sm text-(--foreground) uppercase tracking-tight">{r.name}</span>
                                                <span className="text-[10px] text-gray-400 mt-0.5">@ {formatIDR(r.price)}</span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-6 text-center">
                                            <span className={`px-2 py-1 rounded text-[10px] font-black uppercase tracking-widest ${r.is_active ? 'bg-emerald-500/10 text-emerald-600' : 'bg-rose-500/10 text-rose-600'}`}>
                                                {r.is_active ? 'Active' : 'Inactive'}
                                            </span>
                                        </td>
                                        <td className="px-6 py-6 text-center font-black text-lg">{r.volume}</td>
                                        <td className="px-6 py-6 text-center">
                                            <div className="flex flex-col items-center">
                                                <span className="font-black text-sm">{r.stock}</span>
                                                <span className="text-[10px] text-gray-400 font-bold uppercase tracking-widest">/ {r.total_quota}</span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-6 text-right font-black text-sm">{formatIDR(r.revenue)}</td>
                                        <td className="px-6 py-6 text-right font-black text-sm text-emerald-600">{formatIDR(r.saldo)}</td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                        {!loading && reports.length > 0 && (
                            <tfoot className="bg-(--background) font-black border-t-2 border-(--card-border)">
                                <tr>
                                    <td colSpan={3} className="px-6 py-6 text-right uppercase tracking-[0.2em] text-xs text-gray-500">Total</td>
                                    <td className="px-6 py-6 text-center text-lg">{totals.volume}</td>
                                    <td className="px-6 py-6"></td>
                                    <td className="px-6 py-6 text-right text-sm">{formatIDR(totals.revenue)}</td>
                                    <td className="px-6 py-6 text-right text-sm text-emerald-600">{formatIDR(totals.saldo)}</td>
                                </tr>
                            </tfoot>
                        )}
                    </table>
                </div>
            </div>
        </div>
    );
}

function StatCard({ label, value, icon, color, bg }: { label: string, value: string, icon: React.ReactNode, color: string, bg: string }) {
    return (
        <div className="bg-(--card) p-6 rounded-2xl border border-(--card-border) shadow-sm group hover:border-primary/50 transition-all duration-300">
            <div className="flex items-start justify-between">
                <div className={`${bg} ${color} p-3 rounded-xl mb-4 group-hover:scale-110 transition-transform`}>
                    {icon}
                </div>
            </div>
            <div className="flex flex-col">
                <span className="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em] mb-1">{label}</span>
                <span className={`text-xl font-black ${color} tracking-tight`}>{value}</span>
            </div>
        </div>
    );
}
