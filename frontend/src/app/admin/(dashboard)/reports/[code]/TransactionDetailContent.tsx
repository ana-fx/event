"use client";

import { useEffect, useState } from "react";
import axiosInstance from "@/lib/axios";
import { useParams, useRouter } from "next/navigation";
import Link from "next/link";
import { ArrowLeft, Mail, Phone, User, CreditCard, Tag, MapPin } from "lucide-react";
import { toast } from "react-hot-toast";

interface TransactionItem {
    id: number;
    name: string;
    price: number;
    quantity: number;
}

interface Transaction {
    id: number;
    code: string;
    event_id: number;
    ticket_id: number;
    name: string;
    email: string;
    phone: string;
    city: string;
    nik: string;
    gender: string;
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    quantity: any;
    total_price: number;
    status: string;
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    snap_token: any;
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    payment_type: any;
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    midtrans_transaction_id: any;
    created_at: string;
    event_name?: string;
    ticket_name?: string;
    items?: TransactionItem[];
}

export default function TransactionDetailContent() {
    const params = useParams();
    const router = useRouter();
    const [transaction, setTransaction] = useState<Transaction | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        if (params.code) {
            fetchTransaction(params.code as string);
        }
    }, [params.code]);

    const fetchTransaction = async (code: string) => {
        try {
            const res = await axiosInstance.get(`/admin/reports/detail?code=${code}`);
            setTransaction(res.data);
        } catch (error) {
            toast.error("Failed to load transaction details");
            console.error(error);
            router.push("/admin/reports");
        } finally {
            setLoading(false);
        }
    };

    const formatIDR = (price: number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(price);
    };

    const getStatusStyle = (status: string) => {
        switch (status?.toLowerCase()) {
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

    if (loading) {
        return (
            <div className="flex flex-col items-center justify-center min-h-[60vh] gap-3">
                <div className="animate-spin rounded-full h-10 w-10 border-b-2 border-blue-600 shadow-lg shadow-blue-500/20"></div>
                <p className="text-xs font-bold uppercase tracking-widest text-gray-400">Loading Details...</p>
            </div>
        );
    }

    if (!transaction) return null;

    // Helper to safely get string from potential sql.NullString object
    // eslint-disable-next-line @typescript-eslint/no-explicit-any
    const getString = (val: any) => {
        if (val && typeof val === 'object' && 'String' in val) {
            return val.Valid ? val.String : '';
        }
        return val || '';
    };

    const paymentType = getString(transaction.payment_type);
    const midtransID = getString(transaction.midtrans_transaction_id);
    const snapToken = getString(transaction.snap_token);

    return (
        <div className="space-y-8 animate-fade-in max-w-5xl mx-auto">
            {/* Header */}
            <div className="flex items-center gap-4">
                <Link
                    href="/admin/reports"
                    className="p-2 rounded-xl bg-white border border-gray-200 hover:bg-gray-50 text-gray-500 transition-colors"
                >
                    <ArrowLeft className="w-5 h-5" />
                </Link>
                <div>
                    <div className="flex items-center gap-3">
                        <h1 className="text-2xl font-bold text-(--foreground)">Transaction #{transaction.code}</h1>
                        <span className={`inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border ${getStatusStyle(transaction.status)}`}>
                            {transaction.status}
                        </span>
                    </div>
                    <p className="text-gray-500 text-sm mt-1">
                        Placed on {new Date(transaction.created_at).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' })}
                    </p>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Left Column: Customer & Payment Info */}
                <div className="space-y-8">
                    {/* Customer Card */}
                    <div className="bg-(--card) rounded-3xl border border-(--card-border) p-6 sm:p-8 shadow-sm space-y-6">
                        <div className="flex items-center gap-3 pb-6 border-b border-(--card-border)">
                            <div className="w-10 h-10 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center">
                                <User className="w-5 h-5" />
                            </div>
                            <h3 className="font-bold text-lg text-(--foreground)">Customer Details</h3>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Full Name</p>
                                <p className="text-sm font-bold text-(--foreground) uppercase">{transaction.name}</p>
                            </div>
                            <div>
                                <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Email Address</p>
                                <div className="flex items-center gap-2 text-(--foreground)">
                                    <Mail className="w-3.5 h-3.5 text-gray-400" />
                                    <span className="text-sm font-medium">{transaction.email}</span>
                                </div>
                            </div>
                            <div>
                                <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Phone Number</p>
                                <div className="flex items-center gap-2 text-(--foreground)">
                                    <Phone className="w-3.5 h-3.5 text-gray-400" />
                                    <span className="text-sm font-medium">{transaction.phone}</span>
                                </div>
                            </div>
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">NIK</p>
                                    <p className="text-sm font-medium text-(--foreground)">{transaction.nik || '-'}</p>
                                </div>
                                <div>
                                    <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Gender</p>
                                    <p className="text-sm font-medium text-(--foreground) capitalize">{transaction.gender || '-'}</p>
                                </div>
                            </div>
                            <div>
                                <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">City</p>
                                <div className="flex items-center gap-2 text-(--foreground)">
                                    <MapPin className="w-3.5 h-3.5 text-gray-400" />
                                    <span className="text-sm font-medium capitalize">{transaction.city || '-'}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Payment Info Card */}
                    <div className="bg-(--card) rounded-3xl border border-(--card-border) p-6 sm:p-8 shadow-sm space-y-6">
                        <div className="flex items-center gap-3 pb-6 border-b border-(--card-border)">
                            <div className="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center">
                                <CreditCard className="w-5 h-5" />
                            </div>
                            <h3 className="font-bold text-lg text-(--foreground)">Payment Information</h3>
                        </div>

                        <div className="space-y-4">
                            <div>
                                <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Payment Method</p>
                                <p className="text-sm font-bold text-(--foreground) uppercase">{paymentType?.replace(/_/g, ' ') || 'Not Selected'}</p>
                            </div>
                            <div>
                                <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Midtrans ID</p>
                                <p className="text-xs font-mono bg-gray-50 text-gray-600 px-2 py-1 rounded border border-gray-200 inline-block">
                                    {midtransID || '-'}
                                </p>
                            </div>
                            <div>
                                <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Snap Token</p>
                                <p className="text-xs font-mono text-gray-500 truncate" title={snapToken}>
                                    {snapToken || '-'}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right Column: Order Details */}
                <div className="lg:col-span-2 space-y-8">
                    <div className="bg-(--card) rounded-3xl border border-(--card-border) p-6 sm:p-8 shadow-sm h-full">
                        <div className="flex items-center justify-between gap-3 pb-8 border-b border-(--card-border)">
                            <div className="flex items-center gap-3">
                                <div className="w-10 h-10 rounded-full bg-amber-50 text-amber-600 flex items-center justify-center">
                                    <Tag className="w-5 h-5" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-lg text-(--foreground)">Order Summary</h3>
                                    <p className="text-sm text-gray-500">{transaction.event_name}</p>
                                </div>
                            </div>
                            <div className="text-right">
                                <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-1">Total Amount</p>
                                <p className="text-2xl font-black text-blue-600 font-heading">{formatIDR(transaction.total_price)}</p>
                            </div>
                        </div>

                        {/* Items List */}
                        <div className="mt-8 space-y-4">
                            <div className="grid grid-cols-12 gap-4 pb-4 border-b border-gray-100 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                <div className="col-span-6">Item</div>
                                <div className="col-span-2 text-center">Price</div>
                                <div className="col-span-2 text-center">Qty</div>
                                <div className="col-span-2 text-right">Total</div>
                            </div>

                            {transaction.items && transaction.items.length > 0 ? (
                                transaction.items.map((item) => (
                                    <div key={item.id} className="grid grid-cols-12 gap-4 items-center py-4 border-b border-dashed border-gray-100 last:border-0 hover:bg-gray-50/50 transition-colors rounded-lg px-2 -mx-2">
                                        <div className="col-span-6">
                                            <p className="font-bold text-(--foreground) text-sm">{item.name}</p>
                                            <p className="text-[10px] text-gray-400 uppercase tracking-wider">Ticket</p>
                                        </div>
                                        <div className="col-span-2 text-center text-sm font-medium text-gray-600">
                                            {formatIDR(item.price)}
                                        </div>
                                        <div className="col-span-2 text-center">
                                            <span className="inline-block px-2 py-1 rounded bg-gray-100 text-xs font-bold text-gray-600">
                                                x{item.quantity}
                                            </span>
                                        </div>
                                        <div className="col-span-2 text-right font-bold text-(--foreground) text-sm">
                                            {formatIDR(item.price * item.quantity)}
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <div className="grid grid-cols-12 gap-4 items-center py-4 border-b border-dashed border-gray-100 last:border-0 px-2 -mx-2">
                                    <div className="col-span-6">
                                        <p className="font-bold text-(--foreground) text-sm">{transaction.ticket_name}</p>
                                        <p className="text-[10px] text-gray-400 uppercase tracking-wider">Ticket (Legacy)</p>
                                    </div>
                                    <div className="col-span-2 text-center text-sm font-medium text-gray-600">
                                        {formatIDR(transaction.total_price / (transaction.quantity?.Int64 ?? transaction.quantity ?? 1))}
                                    </div>
                                    <div className="col-span-2 text-center">
                                        <span className="inline-block px-2 py-1 rounded bg-gray-100 text-xs font-bold text-gray-600">
                                            x{(transaction.quantity?.Int64 ?? transaction.quantity ?? 1)}
                                        </span>
                                    </div>
                                    <div className="col-span-2 text-right font-bold text-(--foreground) text-sm">
                                        {formatIDR(transaction.total_price)}
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Totals Footer */}
                        <div className="mt-8 bg-gray-50 rounded-2xl p-6">
                            <div className="flex justify-between items-center">
                                <span className="text-sm font-bold text-gray-500 uppercase tracking-wider">Net Total Paid</span>
                                <span className="text-xl font-black text-(--foreground) font-heading">{formatIDR(transaction.total_price)}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
