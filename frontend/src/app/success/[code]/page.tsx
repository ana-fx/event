"use client";

import { useEffect, useState, use } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import { Loader2, CheckCircle, ArrowRight, Download, Calendar, Receipt, User, ShieldCheck } from "lucide-react";
import { toast } from "react-hot-toast";
import api from "@/lib/axios";

export default function SuccessPage({ params }: { params: Promise<{ code: string }> }) {
    const router = useRouter();
    const { code } = use(params);
    
    const [transaction, setTransaction] = useState<any>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchTransaction = async () => {
            try {
                const res = await api.get(`/transaction/status?code=${code}`);
                setTransaction(res.data);
            } catch (err) {
                console.error("Failed to fetch transaction", err);
                toast.error("Transaction not found");
                router.push("/events");
            } finally {
                setLoading(false);
            }
        };

        if (code) {
            fetchTransaction();
        }
    }, [code, router]);

    const formatIDR = (price: number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(price);
    };

    if (loading) return <div className="min-h-screen flex items-center justify-center bg-white"><Loader2 className="w-8 h-8 animate-spin text-blue-600" /></div>;

    if (!transaction) return null;

    const subtotal = transaction.total_price - 6000;
    const handlingFee = 6000;

    return (
        <div className="min-h-screen bg-[#FDFDFD] font-body selection:bg-blue-600/10">
            <Navbar />

            <main className="max-w-7xl mx-auto px-6 lg:px-10 py-24 sm:py-32 lg:py-48">
                {/* Success Header Area */}
                <div className="mb-12 sm:mb-20 space-y-8">
                    <div className="flex items-center gap-4">
                        <div className="w-16 h-16 rounded-[24px] bg-blue-600 flex items-center justify-center shadow-2xl shadow-blue-600/20">
                            <CheckCircle className="w-8 h-8 text-white" />
                        </div>
                        <div>
                            <h1 className="text-[10px] sm:text-[12px] font-black uppercase tracking-[0.4em] text-blue-600 mb-1 font-heading">Payment Successful</h1>
                            <p className="text-sm font-bold text-gray-400 font-body">Order #{transaction.code}</p>
                        </div>
                    </div>
                    
                    <h2 className="text-4xl sm:text-5xl lg:text-7xl font-black text-[#1A1A1A] tracking-[-0.04em] font-heading leading-tight uppercase">
                        Registration <br className="hidden sm:block" /> <span className="text-gray-300">Confirmed.</span>
                    </h2>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-24 items-start">
                    
                    {/* Left Column: Confirmation Chapters */}
                    <div className="lg:col-span-7 space-y-12 sm:space-y-16">
                        
                        {/* Chapter 01: Next Steps / Tickets */}
                        <section className="space-y-8 sm:space-y-10">
                            <div className="flex items-center gap-4 sm:gap-6">
                                <span className="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-xs sm:text-sm font-heading">01</span>
                                <h3 className="text-lg sm:text-xl font-black text-[#1A1A1A] uppercase tracking-tighter font-heading">Next Steps</h3>
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div className="p-8 rounded-[32px] bg-white border border-gray-100 shadow-sm space-y-4 group hover:border-blue-500/30 transition-colors">
                                    <div className="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                                        <Download className="w-5 h-5 text-blue-600" />
                                    </div>
                                    <div>
                                        <h4 className="text-sm font-black uppercase tracking-tight text-gray-900 font-heading">E-Ticket Ready</h4>
                                        <p className="text-[11px] text-gray-400 font-bold uppercase tracking-widest mt-1 font-body">Sent to your email</p>
                                    </div>
                                </div>
                                <div className="p-8 rounded-[32px] bg-white border border-gray-100 shadow-sm space-y-4 group hover:border-blue-500/30 transition-colors">
                                    <div className="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                                        <Calendar className="w-5 h-5 text-blue-600" />
                                    </div>
                                    <div>
                                        <h4 className="text-sm font-black uppercase tracking-tight text-gray-900 font-heading">Add to Calendar</h4>
                                        <p className="text-[11px] text-gray-400 font-bold uppercase tracking-widest mt-1 font-body">Mark the schedule</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {/* Chapter 02: Proof of Payment (Details) */}
                        <section className="space-y-8 sm:space-y-10">
                            <div className="flex items-center gap-4 sm:gap-6">
                                <span className="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-xs sm:text-sm font-heading">02</span>
                                <h3 className="text-lg sm:text-xl font-black text-[#1A1A1A] uppercase tracking-tighter font-heading">Proof of Payment</h3>
                            </div>

                            <div className="bg-white p-8 sm:p-10 rounded-[40px] border border-gray-100 shadow-sm space-y-10">
                                {/* Ticket Info Block */}
                                <div className="flex items-start gap-6 pb-10 border-b border-gray-50">
                                    <div className="w-14 h-14 rounded-2xl bg-gray-50 flex items-center justify-center flex-shrink-0 ring-1 ring-gray-100">
                                        <Receipt className="w-7 h-7 text-gray-400" />
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 font-heading">Event Details</p>
                                        <h4 className="text-2xl font-black text-gray-950 font-heading uppercase tracking-tight leading-tight">{transaction.event_name}</h4>
                                        <div className="flex items-center gap-3">
                                            <span className="text-[11px] font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-md font-body">{transaction.ticket_name}</span>
                                            <span className="text-gray-300 font-body">&bull;</span>
                                            <span className="text-[11px] font-bold text-gray-500 font-body">{transaction.quantity} Ticket(s)</span>
                                        </div>
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

                            <div className="space-y-6">
                                <div className="flex justify-between items-start">
                                    <div className="space-y-1">
                                        <p className="font-black text-[#1A1A1A] text-[14px] sm:text-[16px] font-heading tracking-tight leading-none uppercase">{transaction.ticket_name}</p>
                                        <p className="text-[11px] sm:text-[12px] font-bold text-gray-400 font-body">Subtotal Amount</p>
                                    </div>
                                    <p className="font-black text-[#1A1A1A] text-[14px] sm:text-[16px] font-heading leading-none">{formatIDR(subtotal)}</p>
                                </div>
                                <div className="flex justify-between items-center text-gray-400 border-t border-gray-50 pt-6">
                                    <p className="text-[11px] sm:text-[12px] font-bold font-body">Taxes & Fees</p>
                                    <p className="font-black text-[14px] font-heading">{formatIDR(handlingFee)}</p>
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
