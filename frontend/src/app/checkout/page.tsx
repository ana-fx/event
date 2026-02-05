"use client";

import { useSearchParams, useRouter } from "next/navigation";
import { useEffect, useState } from "react";
import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import { CheckCircle, CreditCard, Loader2 } from "lucide-react";
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
    const [loading, setLoading] = useState(true);
    const [processing, setProcessing] = useState(false);

    // Form Data
    const [form, setForm] = useState({
        name: "",
        email: "",
        phone: "",
    });

    useEffect(() => {
        if (dataParam) {
            try {
                const parsed = JSON.parse(decodeURIComponent(dataParam));
                setItems(parsed);
            } catch (e) {
                console.error("Failed to parse cart data", e);
                toast.error("Invalid checkout data");
                router.push("/events");
            }
        }
        setLoading(false);
    }, [dataParam, router]);

    const handleChange = (e: React.ChangeEvent<HTMLInputElement>) => {
        setForm({ ...form, [e.target.name]: e.target.value });
    };

    const handlePayment = async (e: React.FormEvent) => {
        e.preventDefault();
        setProcessing(true);

        try {
            // Prepare payload for backend
            const payload = {
                event_id: Number(eventIdParam),
                tickets: items.map(item => ({
                    ticket_id: item.id,
                    quantity: item.qty
                })),
                customer: {
                    name: form.name,
                    email: form.email,
                    phone: form.phone
                }
            };

            const res = await api.post("/checkout", payload);

            if (res.data.redirect_url) {
                window.location.href = res.data.redirect_url; // Midtrans or Success Page
            } else if (res.data.token) {
                // If utilizing Snap.js frontend integration, handle here.
                // For now assuming backend returns a redirect_url (common for Snap redirect mode)
                // or we can implement Snap popup here if we had the Snap script loaded.
                toast.success("Order created! Redirecting to payment...");
                // Fallback if no redirect_url but token exists (need Snap.js)
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

    if (loading) return <div className="min-h-screen flex items-center justify-center"><Loader2 className="w-8 h-8 animate-spin text-blue-600" /></div>;

    if (items.length === 0) return (
        <div className="min-h-screen flex flex-col items-center justify-center gap-4">
            <h1 className="text-2xl font-bold">Your cart is empty</h1>
            <button onClick={() => router.back()} className="text-blue-600 hover:underline">Go Back</button>
        </div>
    );

    return (
        <div className="min-h-screen bg-gray-50 flex flex-col">
            <Navbar />

            <main className="flex-1 max-w-4xl mx-auto w-full px-6 py-12 mt-20">
                <h1 className="text-3xl font-black text-gray-900 mb-8">Checkout</h1>

                <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {/* Left: Form */}
                    <div className="md:col-span-2 space-y-6">
                        <div className="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <h2 className="text-xl font-bold mb-4 flex items-center gap-2">
                                <CheckCircle className="w-5 h-5 text-blue-600" />
                                Contact Details
                            </h2>
                            <form id="checkout-form" onSubmit={handlePayment} className="space-y-4">
                                <div>
                                    <label className="block text-sm font-bold text-gray-700 mb-1">Full Name</label>
                                    <input type="text" name="name" required value={form.name} onChange={handleChange} className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="John Doe" />
                                </div>
                                <div>
                                    <label className="block text-sm font-bold text-gray-700 mb-1">Email Address</label>
                                    <input type="email" name="email" required value={form.email} onChange={handleChange} className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="john@example.com" />
                                </div>
                                <div>
                                    <label className="block text-sm font-bold text-gray-700 mb-1">Phone Number</label>
                                    <input type="tel" name="phone" required value={form.phone} onChange={handleChange} className="w-full px-4 py-3 rounded-lg border border-gray-200 focus:ring-2 focus:ring-blue-500 outline-none transition-all" placeholder="08123456789" />
                                </div>
                            </form>
                        </div>
                    </div>

                    {/* Right: Summary */}
                    <div className="md:col-span-1">
                        <div className="bg-white p-6 rounded-2xl shadow-xl border border-gray-100 sticky top-24">
                            <h2 className="text-lg font-bold mb-4">Order Summary</h2>

                            <div className="space-y-3 mb-6">
                                {items.map((item) => (
                                    <div key={item.id} className="flex justify-between items-center text-sm">
                                        <span className="text-gray-600">Ticket ID {item.id} <span className="text-xs font-bold bg-gray-100 px-1 rounded">x{item.qty}</span></span>
                                        {/* Since we don't pass price in URL to avoid tampering, we can't show total here without refetching. 
                                            For MVP, we just show item count or fetch details. 
                                            Assuming backend handles total calculation. 
                                        */}
                                    </div>
                                ))}
                            </div>

                            <div className="border-t border-gray-100 pt-4 mb-6">
                                <p className="text-xs text-gray-400 mb-2">Total Price will be calculated at payment.</p>
                            </div>

                            <button
                                type="submit"
                                form="checkout-form"
                                disabled={processing}
                                className="w-full py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 hover:bg-blue-700 transition-all flex items-center justify-center gap-2"
                            >
                                {processing ? <Loader2 className="w-4 h-4 animate-spin" /> : <CreditCard className="w-4 h-4" />}
                                Pay Now
                            </button>
                        </div>
                    </div>
                </div>
            </main>

            <Footer />
        </div>
    );
}
