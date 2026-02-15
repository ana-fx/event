"use client";

import { useSearchParams, useRouter } from "next/navigation";
import { useEffect, useState, Suspense } from "react";
import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import { CheckCircle, ChevronRight, Loader2 } from "lucide-react";
import { toast } from "react-hot-toast";
import api from "@/lib/axios";

interface CartItem {
    id: number;
    qty: number;
    name?: string;
    price?: number;
}

function CheckoutContent({ params }: { params: { slug: string } }) {
    const searchParams = useSearchParams();
    const router = useRouter();
    const tParam = searchParams.get("t");

    const [items, setItems] = useState<CartItem[]>([]);
    const [tickets, setTickets] = useState<any[]>([]);
    const [event, setEvent] = useState<any>(null);
    const [loading, setLoading] = useState(true);
    const [processing, setProcessing] = useState(false);

    // Form Data
    const [form, setForm] = useState({
        name: "",
        email: "",
        phone: "",
        nik: "",
        gender: "Male",
        city: "",
        agreeTerms: false,
        confirmData: false
    });

    useEffect(() => {
        const initCheckout = async () => {
            const slug = params.slug;
            if (tParam && slug) {
                try {
                    // 1. Parse Ticket Data (id1-qty1,id2-qty2)
                    const parsed = tParam.split(',').map(pair => {
                        const [id, qty] = pair.split('-');
                        return { id: Number(id), qty: Number(qty) };
                    });
                    setItems(parsed);

                    // 2. Fetch Event & Ticket Details by Slug
                    const apiUrl = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8080/api";
                    const res = await fetch(`${apiUrl}/events/detail?slug=${slug}`);
                    if (!res.ok) {
                        const text = await res.text();
                        console.error("Backend error:", text);
                        throw new Error(text);
                    }
                    const data = await res.json();

                    if (data && data.event) {
                        setEvent(data.event);
                    }
                    if (data && data.tickets) {
                        setTickets(data.tickets);
                    }
                } catch (e) {
                    console.error("Failed to initialize checkout", e);
                    toast.error("Invalid checkout data");
                    router.push("/events");
                }
            }
            setLoading(false);
        };

        initCheckout();
    }, [tParam, params, router]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        const { name, value, type } = e.target;
        if (type === 'checkbox') {
            const checked = (e.target as HTMLInputElement).checked;
            setForm({ ...form, [name]: checked });
        } else {
            // Numeric validation for NIK and Phone
            if (name === "nik" || name === "phone") {
                const numericValue = value.replace(/\D/g, "");
                setForm({ ...form, [name]: numericValue });
            } else {
                setForm({ ...form, [name]: value });
            }
        }
    };

    const calculateSubtotal = () => {
        return items.reduce((acc, item) => {
            const ticket = tickets.find(t => t.id === item.id);
            return acc + (ticket ? ticket.price * item.qty : 0);
        }, 0);
    };

    const handlingFee = 6000;
    const subtotal = calculateSubtotal();
    const total = subtotal + (subtotal > 0 ? handlingFee : 0);

    const formatIDR = (price: number) => {
        return new Intl.NumberFormat("id-ID", {
            style: "currency",
            currency: "IDR",
            minimumFractionDigits: 0,
        }).format(price);
    };

    const handlePayment = async (e: React.FormEvent) => {
        e.preventDefault();

        if (!form.agreeTerms || !form.confirmData) {
            toast.error("Please agree to the terms and confirm your data.");
            return;
        }

        setProcessing(true);

        try {
            const firstItem = items[0];
            const payload = {
                event_id: event?.id,
                ticket_id: firstItem.id,
                quantity: firstItem.qty,
                name: form.name,
                email: form.email,
                phone: form.phone,
                nik: form.nik,
                gender: form.gender,
                city: form.city
            };

            const res = await api.post("/checkout", payload);

            if (res.data.transaction?.code) {
                toast.success("Order initiated!");
                router.push(`/payment/${res.data.transaction.code}`);
            } else {
                toast.success("Order successful!");
                router.push("/success");
            }
        } catch (error) {
            console.error(error);
            toast.error("Checkout failed. Please try again.");
        } finally {
            setProcessing(false);
        }
    };

    if (loading) return <div className="min-h-screen flex items-center justify-center bg-white"><Loader2 className="w-8 h-8 animate-spin text-blue-600" /></div>;

    if (items.length === 0) return (
        <div className="min-h-screen flex flex-col items-center justify-center gap-4 bg-white">
            <h1 className="text-2xl font-black text-gray-900 uppercase tracking-tighter font-heading">Cart Empty</h1>
            <button onClick={() => router.back()} className="px-8 py-3 bg-gray-950 text-white rounded-full font-black uppercase text-[10px] tracking-widest hover:bg-blue-600 transition-all font-heading">Go Back</button>
        </div>
    );

    return (
        <div className="min-h-screen bg-[#FDFDFD] font-body selection:bg-blue-600/10">
            <Navbar />

            <main className="max-w-7xl mx-auto px-6 lg:px-10 py-24 sm:py-32 lg:py-48">
                {/* Header Title */}
                <div className="mb-12 sm:mb-20">
                    <h1 className="text-[10px] sm:text-[12px] font-black uppercase tracking-[0.4em] text-blue-600 mb-4 font-heading">Secure Checkout</h1>
                    <h2 className="text-4xl sm:text-5xl lg:text-7xl font-black text-[#1A1A1A] tracking-[-0.04em] font-heading leading-tight">
                        Complete your <br className="hidden sm:block" /> <span className="text-gray-300">Registration</span>
                    </h2>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-24 items-start">

                    {/* Left: Form Section */}
                    <div className="lg:col-span-7">
                        <form onSubmit={handlePayment} className="space-y-10 sm:space-y-14">
                            {/* Identity Group */}
                            <section className="space-y-6 sm:space-y-10">
                                <div className="flex items-center gap-4 sm:gap-6">
                                    <span className="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-xs sm:text-sm font-heading">01</span>
                                    <h3 className="text-lg sm:text-xl font-black text-[#1A1A1A] uppercase tracking-tighter font-heading">Identity Verification</h3>
                                </div>

                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-6 sm:gap-x-10 sm:gap-y-8">
                                    {/* IDENTITY NUMBER */}
                                    <div className="space-y-2">
                                        <label className="block text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 font-heading ml-2">
                                            NIK/ID NUMBER
                                        </label>
                                        <input
                                            type="text" name="nik" required
                                            inputMode="numeric"
                                            value={form.nik} onChange={handleChange}
                                            className="w-full px-5 py-4 rounded-xl bg-white border border-slate-200 shadow-sm focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-medium text-gray-900 placeholder:text-gray-300"
                                            placeholder="16-digit NIK"
                                        />
                                    </div>

                                    {/* GENDER */}
                                    <div className="space-y-2">
                                        <label className="block text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 font-heading ml-2">
                                            Gender
                                        </label>
                                        <div className="relative">
                                            <select
                                                name="gender" required
                                                value={form.gender} onChange={handleChange}
                                                className="w-full px-5 py-4 rounded-xl bg-white border border-slate-200 shadow-sm focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-medium text-gray-900 appearance-none cursor-pointer"
                                            >
                                                <option value="Male">Male</option>
                                                <option value="Female">Female</option>
                                            </select>
                                            <div className="absolute right-6 sm:right-8 top-1/2 -translate-y-1/2 pointer-events-none text-gray-300">
                                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7"></path></svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </section>

                            {/* Personal Information Group */}
                            <section className="space-y-6 sm:space-y-10">
                                <div className="flex items-center gap-4 sm:gap-6">
                                    <span className="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-black text-xs sm:text-sm font-heading">02</span>
                                    <h3 className="text-lg sm:text-xl font-black text-[#1A1A1A] uppercase tracking-tighter font-heading">Personal Information</h3>
                                </div>
                                
                                <div className="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-6 sm:gap-x-10 sm:gap-y-8">
                                    {/* FULL NAME */}
                                    <div className="space-y-2">
                                        <label className="block text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 font-heading ml-2">
                                            Full Name
                                        </label>
                                        <input
                                            type="text" name="name" required
                                            value={form.name} onChange={handleChange}
                                            className="w-full px-5 py-4 rounded-xl bg-white border border-slate-200 shadow-sm focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-medium text-gray-900 placeholder:text-gray-300"
                                            placeholder="Your full name"
                                        />
                                    </div>

                                    {/* EMAIL ADDRESS */}
                                    <div className="space-y-2">
                                        <label className="block text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 font-heading ml-2">
                                            Email Address
                                        </label>
                                        <input
                                            type="email" name="email" required
                                            value={form.email} onChange={handleChange}
                                            className="w-full px-5 py-4 rounded-xl bg-white border border-slate-200 shadow-sm focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-medium text-gray-900 placeholder:text-gray-300"
                                            placeholder="your@email.com"
                                        />
                                    </div>

                                    {/* PHONE NUMBER */}
                                    <div className="space-y-2">
                                        <label className="block text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 font-heading ml-2">
                                            Phone Number
                                        </label>
                                        <input
                                            type="tel" name="phone" required
                                            inputMode="numeric"
                                            value={form.phone} onChange={handleChange}
                                            className="w-full px-5 py-4 rounded-xl bg-white border border-slate-200 shadow-sm focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-medium text-gray-900 placeholder:text-gray-300"
                                            placeholder="+62"
                                        />
                                    </div>

                                    {/* CITY */}
                                    <div className="space-y-2">
                                        <label className="block text-[10px] font-black uppercase tracking-[0.15em] text-gray-400 font-heading ml-2">
                                            City of Residence
                                        </label>
                                        <input
                                            type="text" name="city" required
                                            value={form.city} onChange={handleChange}
                                            className="w-full px-5 py-4 rounded-xl bg-white border border-slate-200 shadow-sm focus:ring-4 focus:ring-blue-600/5 focus:border-blue-600 outline-none transition-all text-sm font-medium text-gray-900 placeholder:text-gray-300"
                                            placeholder="City"
                                        />
                                    </div>
                                </div>
                            </section>

                            {/* Aggrement Group */}
                            <div className="space-y-4 !mt-0">
                                <div className="space-y-4">
                                    <label className="flex items-center gap-4 sm:gap-5 cursor-pointer group">
                                        <div className="relative flex items-center">
                                            <input
                                                type="checkbox" name="agreeTerms"
                                                checked={form.agreeTerms} onChange={handleChange}
                                                className="peer w-5 h-5 sm:w-6 sm:h-6 rounded-[6px] sm:rounded-[8px] border-2 border-gray-100 text-blue-600 focus:ring-blue-600 transition-all cursor-pointer appearance-none checked:bg-blue-600 checked:border-blue-600"
                                            />
                                            <CheckCircle className="absolute w-3 h-3 sm:w-4 sm:h-4 text-white left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" />
                                        </div>
                                        <p className="text-[12px] sm:text-[13px] font-bold text-gray-500 group-hover:text-gray-900 transition-colors">
                                            I agree to the <span className="text-blue-600 font-black">Ticket Terms</span> and <span className="text-blue-600 font-black">Privacy Policy</span>.
                                        </p>
                                    </label>
                                    <label className="flex items-center gap-4 sm:gap-5 cursor-pointer group">
                                        <div className="relative flex items-center">
                                            <input
                                                type="checkbox" name="confirmData"
                                                checked={form.confirmData} onChange={handleChange}
                                                className="peer w-5 h-5 sm:w-6 sm:h-6 rounded-[6px] sm:rounded-[8px] border-2 border-gray-100 text-blue-600 focus:ring-blue-600 transition-all cursor-pointer appearance-none checked:bg-blue-600 checked:border-blue-600"
                                            />
                                            <CheckCircle className="absolute w-3 h-3 sm:w-4 sm:h-4 text-white left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" />
                                        </div>
                                        <p className="text-[12px] sm:text-[13px] font-bold text-gray-500 group-hover:text-gray-900 transition-colors">
                                            I confirm that the details provided above are <span className="text-gray-900 font-black">accurate</span>.
                                        </p>
                                    </label>
                                </div>

                                {/* Main Button under agreement */}
                                <div className="pt-8">
                                    <button
                                        type="submit"
                                        disabled={processing}
                                        className="w-full sm:w-fit sm:min-w-[300px] py-5 sm:py-6 px-12 bg-gray-950 text-white font-black rounded-[24px] sm:rounded-[32px] shadow-2xl shadow-gray-950/20 hover:bg-blue-600 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-4 text-[14px] sm:text-[15px] group font-heading tracking-widest uppercase"
                                    >
                                        {processing ? (
                                            <Loader2 className="w-5 h-5 sm:w-6 sm:h-6 animate-spin" />
                                        ) : (
                                            <>
                                                Pay & Confirm Order
                                                <ChevronRight className="w-5 h-5 sm:w-6 sm:h-6 group-hover:translate-x-1.5 transition-transform" />
                                            </>
                                        )}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                    {/* Right: Summary Section */}
                    <div className="lg:col-span-5 space-y-6 sm:space-y-8 lg:sticky lg:top-36">
                        {/* Event Compact Info */}
                        {event && (
                            <div className="bg-white overflow-hidden rounded-[32px] sm:rounded-[40px] border border-gray-100/50 shadow-sm transition-all group p-1.5 ring-1 ring-gray-100/30">
                                <div className="flex items-center gap-4 sm:gap-6 p-3 sm:p-4">
                                    <div className="relative w-20 h-20 sm:w-28 sm:h-28 flex-shrink-0 overflow-hidden rounded-[20px] sm:rounded-[28px] bg-gray-50">
                                        {event.thumbnail_path ? (
                                            <img 
                                                src={`${process.env.NEXT_PUBLIC_BASE_URL || 'http://localhost:8080'}${event.thumbnail_path}`}
                                                alt={event.name}
                                                className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500"
                                            />
                                        ) : (
                                            <div className="w-full h-full bg-blue-50 flex items-center justify-center text-blue-200 uppercase font-black text-lg">
                                                {event.name?.[0]}
                                            </div>
                                        )}
                                    </div>
                                    <div className="space-y-2 sm:space-y-3">
                                        <span className="inline-block px-2 sm:px-3 py-1 bg-stone-100 text-stone-500 rounded-full text-[8px] sm:text-[9px] font-black uppercase tracking-widest">{event.category}</span>
                                        <h4 className="text-lg sm:text-xl font-black text-[#1A1A1A] leading-tight font-heading group-hover:text-blue-600 transition-colors line-clamp-2">{event.name}</h4>
                                        <div className="flex flex-col gap-0.5 sm:gap-1">
                                            <p className="text-[10px] sm:text-[11px] font-bold text-gray-400 font-body uppercase tracking-wider">{new Date(event.start_date).toLocaleDateString('en-US', { day: '2-digit', month: 'long', year: 'numeric' })}</p>
                                            <p className="text-[10px] sm:text-[11px] font-bold text-gray-400 font-body uppercase tracking-wider line-clamp-1">{event.location}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        )}

                        {/* Order Details Card */}
                        <div className="bg-white p-8 sm:p-10 lg:p-12 rounded-[32px] sm:rounded-[48px] border border-gray-100 shadow-2xl shadow-gray-200/40">
                            <h3 className="text-[9px] sm:text-[10px] font-black uppercase tracking-[0.3em] text-[#B1B1B1] mb-8 sm:mb-12 font-heading text-center">
                                Registration Summary
                            </h3>

                            <div className="space-y-6 sm:space-y-8 mb-10 sm:mb-14">
                                {items.map((item) => {
                                    const ticket = tickets.find(t => t.id === item.id);
                                    return (
                                        <div key={item.id} className="flex justify-between items-start group border-b border-gray-50 pb-6 last:border-0 last:pb-0">
                                            <div className="space-y-1">
                                                <p className="font-black text-[#1A1A1A] text-[14px] sm:text-[16px] font-heading tracking-tight leading-none">{ticket?.name || `Category #${item.id}`}</p>
                                                <p className="text-[11px] sm:text-[12px] font-bold text-gray-400 font-body">{item.qty} Ticket{item.qty > 1 ? 's' : ''} &bull; {formatIDR(ticket?.price || 0)}</p>
                                            </div>
                                            <p className="font-black text-[#1A1A1A] text-[14px] sm:text-[16px] font-heading leading-none">{formatIDR((ticket?.price || 0) * item.qty)}</p>
                                        </div>
                                    );
                                })}
                            </div>

                            <div className="pt-6 sm:pt-8 border-t-2 border-dashed border-gray-100 space-y-8 sm:space-y-10">
                                <div>
                                    <div className="flex justify-center mb-3 sm:mb-4">
                                        <p className="text-[9px] sm:text-[10px] font-black uppercase tracking-[0.25em] text-[#D1D1D1] font-heading">Total Amount Due</p>
                                    </div>
                                    <div className="text-center space-y-3 sm:space-y-4">
                                        <p className="text-[40px] sm:text-[48px] lg:text-[56px] font-black text-blue-600 leading-none tracking-[-0.05em] font-heading">
                                            {formatIDR(total)}
                                        </p>
                                        <div className="flex items-center justify-center gap-2 sm:gap-3 py-2 sm:py-3 px-4 sm:px-6 bg-blue-50/40 rounded-2xl w-fit mx-auto ring-1 ring-blue-50/50">
                                            <span className="text-[8px] sm:text-[9px] font-black bg-blue-600 text-white px-2 py-0.5 rounded uppercase tracking-wider font-heading">Inc. Fee</span>
                                            <p className="text-[10px] sm:text-[11px] font-bold text-blue-600/60 font-body">{formatIDR(handlingFee)} Handling Fee</p>
                                        </div>
                                    </div>
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

export default function CheckoutPage({ params }: { params: Promise<{ slug: string }> }) {
    const resolvedParams = typeof (params as any).then === 'function' ? (params as any) : Promise.resolve(params);
    
    return (
        <Suspense fallback={<div className="min-h-screen flex items-center justify-center bg-white"><Loader2 className="w-8 h-8 animate-spin text-blue-600" /></div>}>
            <CheckoutPageInner paramsPromise={resolvedParams} />
        </Suspense>
    );
}

function CheckoutPageInner({ paramsPromise }: { paramsPromise: Promise<{ slug: string }> }) {
    const [params, setParams] = useState<{ slug: string } | null>(null);

    useEffect(() => {
        paramsPromise.then(setParams);
    }, [paramsPromise]);

    if (!params) return <div className="min-h-screen flex items-center justify-center bg-white"><Loader2 className="w-8 h-8 animate-spin text-blue-600" /></div>;

    return <CheckoutContent params={params} />;
}
