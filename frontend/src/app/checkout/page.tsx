"use client";

import { useSearchParams, useRouter } from "next/navigation";
import { useEffect, useState } from "react";
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

export default function CheckoutPage() {
    const searchParams = useSearchParams();
    const router = useRouter();
    const dataParam = searchParams.get("data");
    const eventIdParam = searchParams.get("eventId");

    const [items, setItems] = useState<CartItem[]>([]);
    const [tickets, setTickets] = useState<any[]>([]);
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
            if (dataParam && eventIdParam) {
                try {
                    // 1. Parse Cart Data
                    const parsed = JSON.parse(decodeURIComponent(dataParam));
                    setItems(parsed);

                    // 2. Fetch Event & Ticket Details
                    const apiUrl = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8080/api";
                    const res = await fetch(`${apiUrl}/events/detail?id=${eventIdParam}`);
                    if (!res.ok) {
                        const text = await res.text();
                        console.error("Backend error:", text);
                        throw new Error(text);
                    }
                    const data = await res.json();

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
    }, [dataParam, eventIdParam, router]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLSelectElement>) => {
        const { name, value, type } = e.target;
        if (type === 'checkbox') {
            const checked = (e.target as HTMLInputElement).checked;
            setForm({ ...form, [name]: checked });
        } else {
            setForm({ ...form, [name]: value });
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
                event_id: Number(eventIdParam),
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

            if (res.data.redirect_url) {
                window.location.href = res.data.redirect_url;
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
        <div className="min-h-screen flex flex-col items-center justify-center gap-4 bg-white font-['Outfit']">
            <h1 className="text-2xl font-black text-gray-900 uppercase tracking-tighter">Cart Empty</h1>
            <button onClick={() => router.back()} className="px-8 py-3 bg-gray-900 text-white rounded-full font-bold uppercase text-xs tracking-widest hover:bg-blue-600 transition-all">Go Back</button>
        </div>
    );

    return (
        <div className="min-h-screen bg-[#FDFDFD] font-['Outfit']">
            <Navbar />

            <main className="max-w-7xl mx-auto px-6 lg:px-10 py-32">
                <div className="grid grid-cols-1 lg:grid-cols-12 gap-20 items-start">

                    {/* Form Section */}
                    <div className="lg:col-span-8">
                        <form onSubmit={handlePayment} className="space-y-16">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-x-12 gap-y-12">
                                {/* IDENTITY NUMBER */}
                                <div className="space-y-4">
                                    <label className="block text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]">
                                        Identity Number (NIK/Passport/ID)
                                    </label>
                                    <input
                                        type="text" name="nik" required
                                        value={form.nik} onChange={handleChange}
                                        className="w-full px-8 py-6 rounded-[24px] bg-white border border-gray-100 shadow-sm focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 outline-none transition-all text-[15px] font-semibold text-gray-900 placeholder:text-gray-200"
                                        placeholder="12123122435436547634"
                                    />
                                </div>

                                {/* GENDER */}
                                <div className="space-y-4">
                                    <label className="block text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]">
                                        Gender
                                    </label>
                                    <div className="relative">
                                        <select
                                            name="gender" required
                                            value={form.gender} onChange={handleChange}
                                            className="w-full px-8 py-6 rounded-[24px] bg-white border border-gray-100 shadow-sm focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 outline-none transition-all text-[15px] font-semibold text-gray-900 appearance-none cursor-pointer"
                                        >
                                            <option value="Male">Male</option>
                                            <option value="Female">Female</option>
                                        </select>
                                        <div className="absolute right-8 top-1/2 -translate-y-1/2 pointer-events-none">
                                            <svg className="w-5 h-5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7"></path></svg>
                                        </div>
                                    </div>
                                </div>

                                {/* FULL NAME */}
                                <div className="space-y-4">
                                    <label className="block text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]">
                                        Full Name
                                    </label>
                                    <input
                                        type="text" name="name" required
                                        value={form.name} onChange={handleChange}
                                        className="w-full px-8 py-6 rounded-[24px] bg-white border border-gray-100 shadow-sm focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 outline-none transition-all text-[15px] font-semibold text-gray-900 placeholder:text-gray-200"
                                        placeholder="Ana"
                                    />
                                </div>

                                {/* YOUR EMAIL */}
                                <div className="space-y-4">
                                    <label className="block text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]">
                                        Your Email
                                    </label>
                                    <input
                                        type="email" name="email" required
                                        value={form.email} onChange={handleChange}
                                        className="w-full px-8 py-6 rounded-[24px] bg-white border border-blue-600 shadow-sm focus:ring-2 focus:ring-blue-600/30 outline-none transition-all text-[15px] font-semibold text-gray-900 placeholder:text-gray-200"
                                        placeholder="asd"
                                    />
                                </div>

                                {/* PHONE NUMBER */}
                                <div className="space-y-4">
                                    <label className="block text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]">
                                        Phone Number
                                    </label>
                                    <input
                                        type="tel" name="phone" required
                                        value={form.phone} onChange={handleChange}
                                        className="w-full px-8 py-6 rounded-[24px] bg-white border border-gray-100 shadow-sm focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 outline-none transition-all text-[15px] font-semibold text-gray-900 placeholder:text-gray-200"
                                        placeholder="+62..."
                                    />
                                </div>

                                {/* CITY OF RESIDENCE */}
                                <div className="space-y-4">
                                    <label className="block text-[10px] font-black uppercase tracking-[0.2em] text-[#1A1A1A]">
                                        City of Residence
                                    </label>
                                    <input
                                        type="text" name="city" required
                                        value={form.city} onChange={handleChange}
                                        className="w-full px-8 py-6 rounded-[24px] bg-white border border-gray-100 shadow-sm focus:ring-2 focus:ring-blue-600/30 focus:border-blue-600 outline-none transition-all text-[15px] font-semibold text-gray-900 placeholder:text-gray-200"
                                        placeholder="Jakarta"
                                    />
                                </div>
                            </div>

                            {/* CHECKBOXES */}
                            <div className="space-y-6 pt-12 border-t border-gray-50">
                                <label className="flex items-center gap-5 cursor-pointer group">
                                    <div className="relative flex items-center">
                                        <input
                                            type="checkbox" name="agreeTerms"
                                            checked={form.agreeTerms} onChange={handleChange}
                                            className="peer w-7 h-7 rounded-[10px] border-2 border-gray-200 text-blue-600 focus:ring-blue-600 transition-all cursor-pointer appearance-none checked:bg-blue-600 checked:border-blue-600"
                                        />
                                        <CheckCircle className="absolute w-4 h-4 text-white left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" />
                                    </div>
                                    <span className="text-[14px] font-semibold text-gray-500 group-hover:text-gray-900 transition-colors">
                                        I agree to the <span className="text-blue-600 font-black">Terms</span> and <span className="text-blue-600 font-black">Privacy</span>.
                                    </span>
                                </label>
                                <label className="flex items-center gap-5 cursor-pointer group">
                                    <div className="relative flex items-center">
                                        <input
                                            type="checkbox" name="confirmData"
                                            checked={form.confirmData} onChange={handleChange}
                                            className="peer w-7 h-7 rounded-[10px] border-2 border-gray-200 text-blue-600 focus:ring-blue-600 transition-all cursor-pointer appearance-none checked:bg-blue-600 checked:border-blue-600"
                                        />
                                        <CheckCircle className="absolute w-4 h-4 text-white left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 opacity-0 peer-checked:opacity-100 transition-opacity pointer-events-none" />
                                    </div>
                                    <span className="text-[14px] font-semibold text-gray-500 group-hover:text-gray-900 transition-colors">
                                        I confirm that the data provided is accurate and correct.
                                    </span>
                                </label>
                            </div>
                        </form>
                    </div>

                    {/* Summary Section */}
                    <div className="lg:col-span-4">
                        <div className="bg-white p-10 rounded-[48px] border border-gray-50 shadow-2xl shadow-gray-100/50 sticky top-36">
                            <h2 className="text-[10px] font-black uppercase tracking-[0.3em] text-[#D1D1D1] mb-10">
                                Order Details
                            </h2>

                            <div className="space-y-6 mb-12">
                                {items.map((item) => {
                                    const ticket = tickets.find(t => t.id === item.id);
                                    return (
                                        <div key={item.id} className="flex justify-between items-start">
                                            <div>
                                                <p className="font-extrabold text-[#1A1A1A] text-[15px]">{ticket?.name || `Ticket #${item.id}`}</p>
                                                <p className="text-[13px] font-semibold text-gray-400 mt-1">{item.qty} x {formatIDR(ticket?.price || 0)}</p>
                                            </div>
                                            <p className="font-black text-[#1A1A1A] text-[15px]">{formatIDR((ticket?.price || 0) * item.qty)}</p>
                                        </div>
                                    );
                                })}
                            </div>

                            <div className="pt-10 border-t border-gray-50 flex flex-col gap-8">
                                <div>
                                    <p className="text-[10px] font-black uppercase tracking-[0.25em] text-[#D1D1D1] mb-4 text-center">Total Payable Amount</p>
                                    <div className="text-center">
                                        <p className="text-[44px] font-black text-blue-600 leading-none tracking-[-0.04em]">
                                            {formatIDR(total)}
                                        </p>
                                    </div>
                                    <div className="mt-6 flex items-center justify-center gap-3 bg-gray-50/50 py-3 rounded-2xl">
                                        <span className="text-[9px] font-black bg-[#EBEBEB] text-[#A3A3A3] px-2 py-1 rounded-md uppercase tracking-wider">Inc. Fees</span>
                                        <p className="text-[11px] font-bold text-gray-400">{formatIDR(handlingFee)} Handling Fee</p>
                                    </div>
                                </div>

                                <button
                                    onClick={handlePayment}
                                    disabled={processing}
                                    className="w-full py-6 bg-blue-600 text-white font-black rounded-[28px] shadow-2xl shadow-blue-600/20 hover:scale-[1.02] transition-all flex items-center justify-center gap-4 text-[15px] group"
                                >
                                    {processing ? (
                                        <Loader2 className="w-6 h-6 animate-spin" />
                                    ) : (
                                        <div className="flex items-center gap-4">
                                            Complete Order
                                            <ChevronRight className="w-6 h-6 group-hover:translate-x-1 transition-transform" />
                                        </div>
                                    )}
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </main>

            <Footer />
        </div>
    );
}
