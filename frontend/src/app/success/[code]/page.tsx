"use client";

import { useEffect, useState, use, Fragment } from "react";
import React from 'react';
import { useRouter } from "next/navigation";
import Link from "next/link";
import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import { Loader2, CheckCircle, ArrowRight, Download, Calendar, Receipt, User, ShieldCheck, Clock, AlertCircle } from "lucide-react";
import { toast } from "react-hot-toast";
import api from "@/lib/axios";
import { QRCodeSVG } from "qrcode.react";

export default function SuccessPage({ params }: { params: Promise<{ code: string }> }) {
    const router = useRouter();
    const { code } = use(params);

    const [transaction, setTransaction] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [status, setStatus] = useState<"pending" | "paid">("pending");

    const fetchTransaction = async (sync = false) => {
        try {
            const res = await api.get(`/transaction/status?code=${code}${sync ? '&sync=true' : ''}`);
            const data = res.data;

            // GUARD: Strict Success Page
            if (data.status !== "paid") {
                toast.error("Please complete your payment first.");
                router.replace(`/payment/${code}`);
                return;
            }

            setTransaction(data);
            setStatus("paid");
        } catch (err) {
            console.error("Failed to fetch transaction", err);
            toast.error("Transaction not found");
            router.push("/events");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        if (code) {
            fetchTransaction(true); // Auto-sync on load so user doesn't have to click refresh
        }
    }, [code]);

    const formatIDR = (price: number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(price);
    };

    if (loading) return <div className="min-h-screen flex items-center justify-center bg-white"><Loader2 className="w-8 h-8 animate-spin text-blue-600" /></div>;

    if (!transaction) return null;

    const calculateBreakdown = () => {
        const subtotal = transaction.items?.reduce((acc: number, item: any) => acc + (item.price * item.quantity), 0) || (transaction.ticket_price * (transaction.quantity?.Int64 ?? transaction.quantity ?? 0));
        const qty = transaction.items?.reduce((acc: number, item: any) => acc + item.quantity, 0) || (transaction.quantity?.Int64 ?? transaction.quantity ?? 0);

        let serviceFee = 0;
        if (transaction.admin_fee_type === "fixed") {
            serviceFee = transaction.admin_fee * qty;
        } else {
            serviceFee = subtotal * (transaction.admin_fee / 100);
        }

        let handlingFee = 0;
        if (transaction.organizer_fee_online_type === "fixed") {
            handlingFee = transaction.organizer_fee_online;
        } else {
            handlingFee = subtotal * (transaction.organizer_fee_online / 100);
        }

        let ppn = 0;
        if (transaction.ppn_type === "fixed") {
            ppn = transaction.ppn;
        } else {
            ppn = subtotal * (transaction.ppn / 100);
        }

        return { subtotal, serviceFee, handlingFee, ppn };
    };

    const { subtotal, serviceFee, handlingFee, ppn } = calculateBreakdown();

    return (
        <div className="min-h-screen bg-[#FDFDFD] font-body selection:bg-blue-600/10">
            <Navbar />

            <main className="max-w-7xl mx-auto px-6 lg:px-10 py-24 sm:py-32 lg:py-48">
                {/* Status-based Header Area */}
                <div className="mb-12 sm:mb-20 space-y-8">
                    <div className="flex items-center gap-4">
                        <div className={`w-16 h-16 rounded-[24px] flex items-center justify-center shadow-2xl ${status === 'paid' ? 'bg-blue-600 shadow-blue-600/20' : 'bg-amber-500 shadow-amber-500/20'}`}>
                            {status === 'paid' ? <CheckCircle className="w-8 h-8 text-white" /> : <Clock className="w-8 h-8 text-white" />}
                        </div>
                        <div>
                            <h1 className={`text-[10px] sm:text-[12px] font-black uppercase tracking-[0.4em] mb-1 font-heading ${status === 'paid' ? 'text-blue-600' : 'text-amber-500'}`}>
                                {status === 'paid' ? 'Payment Successful' : 'Awaiting Confirmation'}
                            </h1>
                            <p className="text-sm font-bold text-gray-400 font-body">Order #{transaction.code}</p>
                        </div>
                    </div>

                    <h2 className="text-4xl sm:text-5xl lg:text-7xl font-black text-[#1A1A1A] tracking-[-0.04em] font-heading leading-tight uppercase">
                        {status === 'paid' ? (
                            <>Registration <br className="hidden sm:block" /> <span className="text-gray-300">Confirmed.</span></>
                        ) : (
                            <>Payment <br className="hidden sm:block" /> <span className="text-gray-300">Verification.</span></>
                        )}
                    </h2>

                    {status !== 'paid' && (
                        <div className="flex items-center gap-3 pt-4">
                            <button
                                onClick={() => { setLoading(true); fetchTransaction(true); }}
                                className="px-6 py-3 bg-gray-950 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-blue-600 transition-colors"
                            >
                                Refresh Payment Status
                            </button>
                            <p className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">
                                Status: <span className="text-amber-500 uppercase">{transaction.status}</span>
                            </p>
                        </div>
                    )}

                    {status === 'pending' && transaction.sync_debug && (
                        <div className="mt-4 p-4 bg-gray-50 rounded-xl border border-gray-100 max-w-2xl">
                            <p className="text-[8px] font-black text-gray-400 uppercase tracking-widest mb-2 font-heading">🔧 Debug: Midtrans Sync Status</p>
                            <code className="text-[10px] text-gray-600 font-mono break-all leading-relaxed bg-white p-2 rounded border border-gray-100 block">
                                {transaction.sync_debug}
                            </code>
                        </div>
                    )}

                    {/* Dev Only: Hidden bypass link */}
                    <button
                        onClick={() => fetchTransaction(true)} // Calling with sync=true is normal refresh
                        className="opacity-0 hover:opacity-10 text-[8px] absolute bottom-2 left-2 cursor-default"
                        onDoubleClick={() => {
                            const res = api.get(`/transaction/status?code=${code}&force_paid=true`);
                            toast.promise(res, {
                                loading: 'Dev: Bypassing...',
                                success: () => { fetchTransaction(); return 'Dev: Bypassed to Paid!'; },
                                error: 'Bypass failed'
                            });
                        }}
                    >
                        Dev Bypass (Double Click)
                    </button>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-24 items-start">

                    {/* Left Column: Confirmation Chapters */}
                    <div className="lg:col-span-7 space-y-12 sm:space-y-16">

                        {/* Chapter 01: Proof of Payment (Details) */}
                        <section className="space-y-8 sm:space-y-10">
                            <div className="flex items-center gap-4 sm:gap-6">
                                <span className="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-xs sm:text-sm font-heading">01</span>
                                <h3 className="text-lg sm:text-xl font-black text-[#1A1A1A] uppercase tracking-tighter font-heading">Proof of Payment</h3>
                            </div>

                            <div className="bg-white p-8 sm:p-10 rounded-[40px] border border-gray-100 shadow-sm space-y-10">
                                {/* Ticket Info Block */}
                                <div className="flex items-start gap-6 pb-10 border-b border-gray-50">
                                    <div className="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center shrink-0 ring-1 ring-gray-100">
                                        <Receipt className="w-7 h-7 text-gray-400" />
                                    </div>
                                    <div className="space-y-1 grow">
                                        <p className="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 font-heading">Event Details</p>
                                        <h4 className="text-2xl font-black text-gray-950 font-heading uppercase tracking-tight leading-tight">{transaction.event_name}</h4>
                                        <div className="flex flex-wrap items-center gap-x-3 gap-y-1">
                                            {transaction.items?.length > 0 ? (
                                                transaction.items.map((it: any, idx: number) => (
                                                    <React.Fragment key={it.id}>
                                                        <span className="text-[11px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md font-body">{it.name} ({it.quantity})</span>
                                                        {idx < transaction.items.length - 1 && <span className="text-gray-200 font-body self-center">&bull;</span>}
                                                    </React.Fragment>
                                                ))
                                            ) : (
                                                <span className="text-[11px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md font-body">{transaction.ticket_name} ({(transaction.quantity?.Int64 ?? transaction.quantity)})</span>
                                            )}
                                        </div>
                                    </div>
                                    <div className="hidden sm:block p-3 bg-white border border-gray-100 rounded-2xl shadow-sm">
                                        <QRCodeSVG value={transaction.code} size={80} />
                                    </div>
                                </div>
                                <div className="sm:hidden flex justify-center pb-6 border-b border-gray-50">
                                    <div className="p-4 bg-white border border-gray-100 rounded-3xl shadow-sm">
                                        <QRCodeSVG value={transaction.code} size={150} />
                                    </div>
                                </div>

                                {/* Billing Summary Grid */}
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-x-10 gap-y-8">
                                    <div className="space-y-1">
                                        <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 font-heading">Billing Name</p>
                                        <p className="text-sm font-bold text-gray-950 font-body uppercase">{transaction.name}</p>
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 font-heading">Transaction Date</p>
                                        <p className="text-sm font-bold text-gray-950 font-body uppercase">{new Date(transaction.created_at).toLocaleDateString('en-US', { day: '2-digit', month: 'long', year: 'numeric' })}</p>
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 font-heading">Identity (NIK)</p>
                                        <p className="text-sm font-bold text-gray-950 font-body">{transaction.nik}</p>
                                    </div>
                                    <div className="space-y-1 flex items-center gap-2 pt-2">
                                        <ShieldCheck className="w-4 h-4 text-blue-600" />
                                        <span className="text-[10px] font-black uppercase tracking-widest text-blue-600 font-heading">Payment Verified</span>
                                    </div>
                                </div>

                                {/* Integrated Next Steps */}
                                <div className="pt-10 border-t border-gray-50">
                                    <div className="flex items-center gap-4 mb-6">
                                        <span className="w-6 h-6 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center font-black text-[10px] font-heading">02</span>
                                        <h4 className="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 font-heading">Next Steps</h4>
                                    </div>
                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div className="flex items-center gap-4 p-4 rounded-2xl bg-blue-50/30 border border-blue-100/50 group hover:border-blue-500/30 transition-colors">
                                            <div className="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center shrink-0">
                                                <Download className="w-5 h-5 text-blue-600" />
                                            </div>
                                            <div>
                                                <h4 className="text-[11px] font-black uppercase tracking-tight text-gray-900 font-heading">Ticket Ready</h4>
                                                <p className="text-[9px] text-gray-400 font-bold uppercase tracking-widest font-body">Check your email</p>
                                            </div>
                                        </div>
                                        <div className="flex items-center gap-4 p-4 rounded-2xl bg-blue-50/30 border border-blue-100/50 group hover:border-blue-500/30 transition-colors">
                                            <div className="w-10 h-10 rounded-xl bg-white shadow-sm flex items-center justify-center shrink-0">
                                                <Calendar className="w-5 h-5 text-blue-600" />
                                            </div>
                                            <div>
                                                <h4 className="text-[11px] font-black uppercase tracking-tight text-gray-900 font-heading">Set Calendar</h4>
                                                <p className="text-[9px] text-gray-400 font-bold uppercase tracking-widest font-body">Mark the schedule</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {/* Back Action */}
                        <div className="pt-8">
                            <Link
                                href="/events"
                                className="inline-flex items-center gap-4 px-12 py-6 bg-gray-950 text-white text-xs font-black uppercase tracking-[0.2em] rounded-[28px] hover:bg-blue-600 transition-all shadow-2xl shadow-gray-950/20 group font-heading"
                            >
                                Browse More Events
                                <ArrowRight className="w-5 h-5 group-hover:translate-x-1.5 transition-transform" />
                            </Link>
                        </div>
                    </div>

                    {/* Right Column: Order Totals (Receipt Style) */}
                    <div className="lg:col-span-5 space-y-8 lg:sticky lg:top-36">
                        <div className="bg-white p-8 sm:p-10 lg:p-12 rounded-[32px] sm:rounded-[48px] border border-gray-100 shadow-2xl shadow-gray-200/40 space-y-8 relative overflow-hidden">
                            {/* Success Accent Bar */}
                            <div className="absolute top-0 left-0 right-0 h-1.5 bg-blue-600" />

                            <h3 className="text-[9px] sm:text-[10px] font-black uppercase tracking-[0.3em] text-[#B1B1B1] mb-8 font-heading text-center">
                                Payment Proof Summary
                            </h3>

                            <div className="space-y-4">
                                {transaction.items?.length > 0 ? (
                                    transaction.items.map((item: any) => (
                                        <div key={item.id} className="flex justify-between items-start">
                                            <div className="space-y-1">
                                                <p className="font-black text-[#1A1A1A] text-[14px] sm:text-[16px] font-heading tracking-tight leading-none uppercase">{item.name}</p>
                                                <p className="text-[11px] sm:text-[12px] font-bold text-gray-400 font-body">{item.quantity} Ticket(s) &bull; {formatIDR(item.price)}</p>
                                            </div>
                                            <p className="font-black text-[#1A1A1A] text-[14px] sm:text-[16px] font-heading leading-none">{formatIDR(item.price * item.quantity)}</p>
                                        </div>
                                    ))
                                ) : (
                                    <div className="flex justify-between items-start">
                                        <div className="space-y-1">
                                            <p className="font-black text-[#1A1A1A] text-[14px] sm:text-[16px] font-heading tracking-tight leading-none uppercase">{transaction.ticket_name}</p>
                                            <p className="text-[11px] sm:text-[12px] font-bold text-gray-400 font-body">{(transaction.quantity?.Int64 ?? transaction.quantity)} Ticket(s) &bull; {formatIDR(transaction.ticket_price)}</p>
                                        </div>
                                        <p className="font-black text-[#1A1A1A] text-[14px] sm:text-[16px] font-heading leading-none">{formatIDR(subtotal)}</p>
                                    </div>
                                )}

                                <div className="space-y-3 pt-4 border-t border-gray-50">
                                    {serviceFee > 0 && (
                                        <div className="flex justify-between items-center text-gray-400">
                                            <p className="text-[11px] sm:text-[12px] font-bold font-body uppercase tracking-wider">Service Fee</p>
                                            <p className="font-black text-[13px] font-heading uppercase">{formatIDR(serviceFee)}</p>
                                        </div>
                                    )}
                                    {handlingFee > 0 && (
                                        <div className="flex justify-between items-center text-gray-400">
                                            <p className="text-[11px] sm:text-[12px] font-bold font-body uppercase tracking-wider">Handling Fee</p>
                                            <p className="font-black text-[13px] font-heading uppercase">{formatIDR(handlingFee)}</p>
                                        </div>
                                    )}
                                    {ppn > 0 && (
                                        <div className="flex justify-between items-center text-gray-400">
                                            <p className="text-[11px] sm:text-[12px] font-bold font-body uppercase tracking-wider">PPN</p>
                                            <p className="font-black text-[13px] font-heading uppercase">{formatIDR(ppn)}</p>
                                        </div>
                                    )}
                                </div>
                            </div>

                            <div className="pt-8 border-t-2 border-dashed border-gray-100">
                                <div className="flex justify-center mb-4">
                                    <p className="text-[9px] sm:text-[10px] font-black uppercase tracking-[0.25em] text-[#D1D1D1] font-heading">Total Amount Paid</p>
                                </div>
                                <div className="text-center space-y-4">
                                    <p className="text-[40px] sm:text-[48px] lg:text-[56px] font-black text-blue-600 leading-none tracking-[-0.05em] font-heading">
                                        {formatIDR(transaction.total_price)}
                                    </p>
                                    <div className="flex items-center justify-center gap-2 py-3 px-6 bg-blue-50/40 rounded-2xl w-fit mx-auto ring-1 ring-blue-50/50">
                                        <CheckCircle className="w-4 h-4 text-blue-600" />
                                        <p className="text-[10px] sm:text-[11px] font-bold text-blue-600 font-body uppercase tracking-widest">Transaction Success</p>
                                    </div>
                                </div>
                            </div>

                            <div className="pt-10 flex flex-col items-center gap-4 text-center">
                                <p className="text-[10px] font-bold text-gray-400 font-body max-w-[240px]">
                                    A confirmation email with your e-tickets has been sent to your address.
                                </p>
                                <div className="w-12 h-1 px-4 bg-gray-50 rounded-full" />
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <Footer />
        </div>
    );
}
