"use client";

import { useEffect, useState, Fragment } from "react";
import Link from "next/link";
import axiosInstance from "@/lib/axios";
import { Search, Filter, Download, Calendar, CheckCircle, XCircle, AlertCircle, Activity, Eye, ChevronDown, Check, Clock, Tag } from "lucide-react";
import { Listbox, Transition } from "@headlessui/react";
import { toast } from "react-hot-toast";

interface Transaction {
    id: number;
    code: string;
    event_id: number;
    ticket_id: number;
    name: string;
    email: string;
    phone: string;
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    quantity: any;
    total_price: number;
    status: string;
    created_at: string;
    event_name?: string;
    ticket_name?: string;
    items?: { id: number; name: string; quantity: number }[];
}

export default function OrganizerReportsContent() {
    const [transactions, setTransactions] = useState<Transaction[]>([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState("");
    const [statusFilter, setStatusFilter] = useState("all");

    const fetchTransactions = async () => {
        try {
            const res = await axiosInstance.get("/organizer/reports/transactions");
            setTransactions(res.data || []);
        } catch (error) {
            toast.error("Failed to load transaction report");
            console.error(error);
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        fetchTransactions();
    }, []);

    const formatIDR = (price: number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(price);
    };

    const filteredTransactions = transactions.filter(t => {
        const matchesSearch =
            t.name.toLowerCase().includes(search.toLowerCase()) ||
            t.code.toLowerCase().includes(search.toLowerCase()) ||
            (t.event_name && t.event_name.toLowerCase().includes(search.toLowerCase()));

        const matchesStatus = statusFilter === "all" || t.status === statusFilter;

        return matchesSearch && matchesStatus;
    });

    const getStatusStyle = (status: string) => {
        switch (status.toLowerCase()) {
            case 'paid':
                return 'bg-emerald-500/10 text-emerald-600 border-emerald-500/20';
            case 'pending':
                return 'bg-amber-500/10 text-amber-600 border-amber-500/20';
            case 'failed':
            case 'cancel':
            case 'expire':
                return 'bg-rose-500/10 text-rose-600 border-rose-500/20';
            default:
                return 'bg-gray-500/10 text-gray-600 border-gray-500/20';
        }
    };

    return (
        <div className="space-y-6 animate-fade-in p-4 md:p-8">
            <div className="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
                <div>
                    <h1 className="text-3xl font-bold text-(--foreground)">My Reports</h1>
                    <p className="text-gray-500 text-sm mt-1">Monitor sales and payments for your events.</p>
                </div>
                <button
                    onClick={() => toast.success("Export functionality coming soon!")}
                    className="flex items-center gap-2 px-4 py-2.5 bg-primary text-white font-bold rounded-xl hover:bg-primary transition-all shadow-lg shadow-primary/20 active:scale-95 text-sm uppercase tracking-wider"
                >
                    <Download className="w-4 h-4" />
                    Export CSV
                </button>
            </div>

            {/* Filters Section */}
            <div className="bg-(--card) p-6 rounded-2xl border border-(--card-border) shadow-sm space-y-4 mb-8">
                <div className="grid grid-cols-1 md:grid-cols-12 gap-4">
                    <div className="md:col-span-8 relative group">
                        <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400 group-focus-within:text-primary transition-colors" />
                        <input
                            type="text"
                            placeholder="Search by code, customer name, or event..."
                            className="w-full pl-11 pr-4 py-3 rounded-xl border border-(--card-border) focus:border-primary focus:ring-4 focus:ring-primary/10 outline-none transition-all bg-(--background) text-(--foreground) font-medium"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                        />
                    </div>
                    <div className="md:col-span-4 relative z-10">
                        <Listbox value={statusFilter} onChange={setStatusFilter}>
                            <div className="relative mt-1">
                                <Listbox.Button className="relative w-full cursor-default rounded-xl bg-(--background) py-3 pl-11 pr-10 text-left border border-(--card-border) focus:outline-none focus-visible:border-primary focus-visible:ring-2 focus-visible:ring-white/75 focus-visible:ring-offset-2 focus-visible:ring-offset-primary sm:text-sm shadow-sm">
                                    <span className="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                                        <Filter className="h-5 w-5 text-gray-400" aria-hidden="true" />
                                    </span>
                                    <span className="block truncate font-medium text-(--foreground) capitalize">
                                        {statusFilter === 'all' ? 'All Statuses' : statusFilter}
                                    </span>
                                    <span className="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2">
                                        <ChevronDown
                                            className="h-5 w-5 text-gray-400"
                                            aria-hidden="true"
                                        />
                                    </span>
                                </Listbox.Button>
                                <Transition
                                    as={Fragment}
                                    leave="transition ease-in duration-100"
                                    leaveFrom="opacity-100"
                                    leaveTo="opacity-0"
                                >
                                    <Listbox.Options className="absolute mt-1 max-h-60 w-full overflow-auto rounded-xl bg-(--background) py-1 text-base shadow-lg ring-1 ring-black/5 focus:outline-none sm:text-sm border border-(--card-border)">
                                        {[
                                            { value: 'all', label: 'All Statuses' },
                                            { value: 'paid', label: 'Paid' },
                                            { value: 'pending', label: 'Pending' },
                                            { value: 'cancel', label: 'Cancelled' },
                                        ].map((status, statusIdx) => (
                                            <Listbox.Option
                                                key={statusIdx}
                                                className={({ active }) =>
                                                    `relative cursor-default select-none py-2 pl-10 pr-4 ${active ? 'bg-primary/10 text-primary' : 'text-(--foreground)'
                                                    }`
                                                }
                                                value={status.value}
                                            >
                                                {({ selected }) => (
                                                    <>
                                                        <span
                                                            className={`block truncate ${selected ? 'font-medium' : 'font-normal'
                                                                }`}
                                                        >
                                                            {status.label}
                                                        </span>
                                                        {selected ? (
                                                            <span className="absolute inset-y-0 left-0 flex items-center pl-3 text-primary">
                                                                <Check className="h-5 w-5" aria-hidden="true" />
                                                            </span>
                                                        ) : null}
                                                    </>
                                                )}
                                            </Listbox.Option>
                                        ))}
                                    </Listbox.Options>
                                </Transition>
                            </div>
                        </Listbox>
                    </div>
                </div>
            </div>

            {/* Transactions List */}
            <div className="bg-(--card) rounded-2xl border border-(--card-border) shadow-xl overflow-hidden relative group">
                <div className="overflow-x-auto">
                    <table className="w-full text-left">
                        <thead>
                            <tr className="bg-(--background) border-b border-(--card-border) text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">
                                <th className="px-6 py-5">Code & Customer</th>
                                <th className="px-6 py-5">Event & Ticket</th>
                                <th className="px-6 py-5">Status</th>
                                <th className="px-6 py-5 text-right">Amount</th>
                                <th className="px-6 py-5 text-center">Date</th>
                                <th className="px-6 py-5 text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody className="divide-y divide-(--card-border)">
                            {loading ? (
                                <tr>
                                    <td colSpan={6} className="px-6 py-12 text-center text-gray-500">
                                        <div className="flex flex-col items-center justify-center gap-3">
                                            <div className="animate-spin rounded-full h-10 w-10 border-b-2 border-primary shadow-lg shadow-primary/20"></div>
                                            <p className="text-xs font-bold uppercase tracking-widest text-gray-400">Loading Report...</p>
                                        </div>
                                    </td>
                                </tr>
                            ) : filteredTransactions.length === 0 ? (
                                <tr>
                                    <td colSpan={6} className="px-6 py-12 text-center">
                                        <div className="flex flex-col items-center justify-center gap-2">
                                            <AlertCircle className="w-8 h-8 text-gray-300" />
                                            <p className="text-sm font-bold text-gray-400 uppercase tracking-widest">No transactions found.</p>
                                        </div>
                                    </td>
                                </tr>
                            ) : (
                                filteredTransactions.map((t) => (
                                    <tr key={t.id} className="hover:bg-primary/2 transition-colors group/row">
                                        <td className="px-6 py-5">
                                            <div className="flex flex-col">
                                                <span className="text-xs font-black text-primary tracking-wider mb-1">#{t.code}</span>
                                                <span className="text-sm font-bold text-(--foreground) uppercase line-clamp-1">{t.name}</span>
                                                <span className="text-[11px] text-gray-400 font-medium">{t.email}</span>
                                            </div>
                                        </td>
                                        <td className="px-6 py-5">
                                            <div className="flex flex-col text-sm">
                                                <div className="font-bold text-(--foreground) mb-1">{t.event_name}</div>
                                                <div className="text-xs text-gray-500">{t.ticket_name} (x{t.quantity})</div>
                                            </div>
                                        </td>
                                        <td className="px-6 py-5">
                                            <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border transition-all duration-300 shadow-sm ${getStatusStyle(t.status)}`}>
                                                {t.status === 'paid' && <CheckCircle className="w-3 h-3" />}
                                                {t.status === 'pending' && <Clock className="w-3 h-3" />}
                                                {(t.status === 'cancel' || t.status === 'failed') && <XCircle className="w-3 h-3" />}
                                                {t.status}
                                            </span>
                                        </td>
                                        <td className="px-6 py-5 text-right font-black text-(--foreground)">
                                            {formatIDR(t.total_price)}
                                        </td>
                                        <td className="px-6 py-5 text-center text-xs text-gray-500">
                                            {new Date(t.created_at).toLocaleDateString()}
                                        </td>
                                        <td className="px-6 py-5 text-center">
                                            <Eye className="w-4 h-4 text-gray-400 cursor-not-allowed mx-auto" />
                                        </td>
                                    </tr>
                                ))
                            )}
                        </tbody>
                    </table>
                </div>

                {!loading && filteredTransactions.length > 0 && (
                    <div className="bg-(--background) px-6 py-4 border-t border-(--card-border) flex justify-between items-center whitespace-nowrap overflow-x-auto overflow-y-hidden no-scrollbar">
                        <div className="text-xs font-bold text-gray-400 uppercase tracking-widest">
                            Showing {filteredTransactions.length} records
                        </div>
                        <div className="flex items-center gap-4">
                            <span className="text-[10px] font-black text-gray-400 uppercase tracking-[0.2em]">Total Paid:</span>
                            <span className="text-lg font-black text-primary font-heading tracking-tighter">
                                {formatIDR(filteredTransactions.reduce((acc, t) => acc + (t.status === 'paid' ? t.total_price : 0), 0))}
                            </span>
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}
