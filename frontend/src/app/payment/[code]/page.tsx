"use client";

import { useEffect, useState, use } from "react";
import { useRouter } from "next/navigation";
import Script from "next/script";
import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import { Loader2, ChevronRight, CheckCircle, CreditCard, Receipt, User } from "lucide-react";
import { toast } from "react-hot-toast";
import api from "@/lib/axios";

export default function PaymentPage({ params }: { params: Promise<{ code: string }> }) {
    const router = useRouter();
    const { code } = use(params);
    
    const [transaction, setTransaction] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [processing, setProcessing] = useState(false);

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

    const handlePayNow = () => {
        if (!transaction?.snap_token?.String) {
            toast.error("Payment token not available");
            return;
        }

        setProcessing(true);

        // @ts-ignore
        window.snap.pay(transaction.snap_token.String, {
            onSuccess: function (result: any) {
                toast.success("Payment successful!");
                router.push(`/success/${code}`);
            },
            onPending: function (result: any) {
                toast.success("Payment pending, please complete it.");
                router.push(`/success/${code}`);
            },
            onError: function (result: any) {
                toast.error("Payment failed!");
                setProcessing(false);
            },
            onClose: function () {
                toast.error("Payment closed without completion.");
                setProcessing(false);
            }
        });
    };

    if (loading) return <div className="min-h-screen flex items-center justify-center bg-white"><Loader2 className="w-8 h-8 animate-spin text-blue-600" /></div>;

    if (!transaction) return null;

    const subtotal = transaction.total_price - 6000;
    const handlingFee = 6000;

    return (
        <div className="min-h-screen bg-[#FDFDFD] font-body selection:bg-blue-600/10">
            <Script
                src="https://app.sandbox.midtrans.com/snap/snap.js"
                data-client-key={process.env.NEXT_PUBLIC_MIDTRANS_CLIENT_KEY}
                strategy="lazyOnload"
            />
            <Navbar />

            <main className="max-w-7xl mx-auto px-6 lg:px-10 py-24 sm:py-32 lg:py-48">
                {/* Header Title */}
                <div className="mb-12 sm:mb-20">
                    <h1 className="text-[10px] sm:text-[12px] font-black uppercase tracking-[0.4em] text-blue-600 mb-4 font-heading">Direct Payment</h1>
                    <h2 className="text-4xl sm:text-5xl lg:text-7xl font-black text-[#1A1A1A] tracking-[-0.04em] font-heading leading-tight uppercase">
                        Review & <br className="hidden sm:block" /> <span className="text-gray-300">Complete Payment</span>
                    </h2>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-24 items-start">
                    
                    {/* Left Column: Order Chapters */}
                    <div className="lg:col-span-7 space-y-12 sm:space-y-16">
                        
                        {/* Chapter 01: Order Details */}
                        <section className="space-y-8 sm:space-y-10">
                            <div className="flex items-center gap-4 sm:gap-6">
                                <span className="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-xs sm:text-sm font-heading">01</span>
                                <h3 className="text-lg sm:text-xl font-black text-[#1A1A1A] uppercase tracking-tighter font-heading">Order Details</h3>
                            </div>

                            <div className="bg-white p-8 sm:p-10 rounded-[32px] sm:rounded-[40px] border border-gray-100 shadow-sm space-y-8">
                                <div className="flex items-start gap-6 pb-8 border-b border-gray-50">
                                    <div className="w-12 h-12 rounded-2xl bg-blue-600 flex items-center justify-center flex-shrink-0">
                                        <Receipt className="w-6 h-6 text-white" />
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 font-heading">Event Name</p>
                                        <h4 className="text-xl font-black text-gray-950 font-heading uppercase tracking-tight leading-tight">{transaction.event_name}</h4>
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                    <div className="space-y-1">
                                        <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 font-heading">Ticket Category</p>
                                        <p className="text-sm font-bold text-gray-900 font-body">{transaction.ticket_name}</p>
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 font-heading">Quantity</p>
                                        <p className="text-sm font-bold text-gray-900 font-body">{transaction.quantity} Ticket(s)</p>
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 font-heading">Order ID</p>
                                        <p className="text-sm font-bold text-blue-600 font-body">#{transaction.code}</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {/* Chapter 02: Billing Information */}
                        <section className="space-y-8 sm:space-y-10">
                            <div className="flex items-center gap-4 sm:gap-6">
                                <span className="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-xs sm:text-sm font-heading">02</span>
                                <h3 className="text-lg sm:text-xl font-black text-[#1A1A1A] uppercase tracking-tighter font-heading">Billing Information</h3>
                            </div>

                            <div className="bg-white p-8 sm:p-10 rounded-[32px] sm:rounded-[40px] border border-gray-100 shadow-sm space-y-8">
                                <div className="flex items-start gap-6 pb-8 border-b border-gray-50">
                                    <div className="w-12 h-12 rounded-2xl bg-gray-50 flex items-center justify-center flex-shrink-0 ring-1 ring-gray-100">
                                        <User className="w-6 h-6 text-gray-400" />
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 font-heading">Customer Name</p>
                                        <h4 className="text-xl font-black text-gray-950 font-heading uppercase tracking-tight leading-tight">{transaction.name}</h4>
                                    </div>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-8">
                                    <div className="space-y-1">
                                        <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 font-heading">Email Address</p>
                                        <p className="text-sm font-bold text-gray-900 font-body">{transaction.email}</p>
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 font-heading">Phone Number</p>
                                        <p className="text-sm font-bold text-gray-900 font-body">{transaction.phone || '-'}</p>
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 font-heading">NIK / Passport</p>
                                        <p className="text-sm font-bold text-gray-900 font-body">{transaction.nik || '-'}</p>
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 font-heading">Gender</p>
                                        <p className="text-sm font-bold text-gray-900 font-body">{transaction.gender || '-'}</p>
                                    </div>
                                    <div className="space-y-1">
                                        <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 font-heading">City</p>
                                        <p className="text-sm font-bold text-gray-900 font-body">{transaction.city || '-'}</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {/* Cancel Action */}
                        <div className="pt-4">
                            <button 
                                onClick={() => router.back()}
                                className="text-[10px] font-black uppercase tracking-widest text-gray-300 hover:text-blue-600 transition-colors font-heading flex items-center gap-2 group"
                            >
                                <span className="w-5 h-5 rounded-full border border-gray-200 flex items-center justify-center group-hover:border-blue-600 transition-colors">&larr;</span>
                                Wait, I want to change my details
                            </button>
                        </div>
                    </div>

                    {/* Right Column: Sticky Summary */}
                    <div className="lg:col-span-5 space-y-8 lg:sticky lg:top-36">
                        <div className="bg-white p-8 sm:p-10 lg:p-12 rounded-[32px] sm:rounded-[48px] border border-gray-100 shadow-2xl shadow-gray-200/40 space-y-8">
                            <h3 className="text-[9px] sm:text-[10px] font-black uppercase tracking-[0.3em] text-[#B1B1B1] mb-8 font-heading text-center">
                                Registration Summary
                            </h3>

                            <div className="space-y-6">
                                <div className="flex justify-between items-start group">
                                    <div className="space-y-1">
                                        <p className="font-black text-[#1A1A1A] text-[14px] sm:text-[16px] font-heading tracking-tight leading-none uppercase">{transaction.ticket_name}</p>
                                        <p className="text-[11px] sm:text-[12px] font-bold text-gray-400 font-body">{transaction.quantity} Ticket(s)</p>
                                    </div>
                                    <p className="font-black text-[#1A1A1A] text-[14px] sm:text-[16px] font-heading leading-none">{formatIDR(subtotal)}</p>
                                </div>
                                <div className="flex justify-between items-center text-gray-400 border-t border-gray-50 pt-6">
                                    <p className="text-[11px] sm:text-[12px] font-bold font-body">Service & Handling Fee</p>
                                    <p className="font-black text-[14px] font-heading">{formatIDR(handlingFee)}</p>
                                </div>
                            </div>

                            <div className="pt-8 border-t-2 border-dashed border-gray-100 space-y-10">
                                <div>
                                    <div className="flex justify-center mb-4">
                                        <p className="text-[9px] sm:text-[10px] font-black uppercase tracking-[0.25em] text-[#D1D1D1] font-heading">Total Amount Due</p>
                                    </div>
                                    <div className="text-center space-y-4">
                                        <p className="text-[40px] sm:text-[48px] lg:text-[56px] font-black text-blue-600 leading-none tracking-[-0.05em] font-heading">
                                            {formatIDR(transaction.total_price)}
                                        </p>
                                        <div className="flex items-center justify-center gap-2 py-3 px-6 bg-blue-50/40 rounded-2xl w-fit mx-auto ring-1 ring-blue-50/50">
                                            <span className="text-[8px] sm:text-[9px] font-black bg-blue-600 text-white px-2 py-0.5 rounded uppercase tracking-wider font-heading">Secure</span>
                                            <p className="text-[10px] sm:text-[11px] font-bold text-blue-600/60 font-body">Bank Level Security</p>
                                        </div>
                                    </div>
                                </div>

                                <button
                                    onClick={handlePayNow}
                                    disabled={processing}
                                    className="w-full py-6 px-12 bg-gray-950 text-white font-black rounded-[28px] sm:rounded-[36px] shadow-2xl shadow-gray-950/20 hover:bg-blue-600 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-4 text-xs group font-heading tracking-[0.2em] uppercase"
                                >
                                    {processing ? (
                                        <Loader2 className="w-5 h-5 animate-spin" />
                                    ) : (
                                        <>
                                            Pay Now
                                            <ChevronRight className="w-5 h-5 group-hover:translate-x-1.5 transition-transform" />
                                        </>
                                    )}
                                </button>
                                
                                <div className="flex items-center justify-center gap-3 text-gray-300">
                                    <CreditCard className="w-4 h-4" />
                                    <span className="text-[9px] font-bold uppercase tracking-[0.1em] font-body">Powered by Midtrans Snap</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <Footer />
        </div>
    );
}
