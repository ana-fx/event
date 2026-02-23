"use client";

import { useState } from "react";
import { Copy, MapPin, Calendar, Clock, ChevronDown, ChevronUp, CheckCircle, Ticket as TicketIcon, ShoppingBag, Plus, Minus, ShieldCheck } from "lucide-react";
import { format } from "date-fns";
import toast from "react-hot-toast";
import { cn } from "@/lib/utils";

interface Ticket {
    id: number;
    event_id: number;
    name: string;
    description: { String: string; Valid: boolean };
    price: number;
    quota: number;
    max_purchase_per_user: number;
    start_date: string;
    end_date: string;
    is_active: boolean;
}

export default function TicketSelector({ tickets, eventSlug }: { tickets: Ticket[], eventSlug: string }) {
    const [selection, setSelection] = useState<{ [key: number]: number }>({});

    const updateQuantity = (ticketId: number, delta: number, max: number) => {
        setSelection(prev => {
            const current = prev[ticketId] || 0;
            const next = Math.max(0, Math.min(max, current + delta));
            return { ...prev, [ticketId]: next };
        });
    };

    const totalQty = Object.values(selection).reduce((a, b) => a + b, 0);
    const totalPrice = Object.entries(selection).reduce((acc, [id, qty]) => {
        const ticket = tickets.find(t => t.id === Number(id));
        return acc + (ticket ? ticket.price * qty : 0);
    }, 0);

    const handleCheckout = () => {
        if (totalQty === 0) {
            toast.error("Please select at least one ticket");
            return;
        }

        const selectedTickets = Object.entries(selection)
            .filter(([_, qty]) => qty > 0)
            .map(([id, qty]) => `${id}-${qty}`)
            .join(',');

        window.location.href = `/checkout/${eventSlug}?t=${selectedTickets}`;
    };

    const activeTickets = (tickets || []).filter(t => t?.is_active);

    return (
        <div className="group relative">
            {/* Main Background with Glassmorphism */}
            <div className="relative bg-white/70 backdrop-blur-3xl rounded-[40px] border border-white shadow-2xl overflow-hidden transition-all duration-500 group-hover:shadow-primary/10">

                {/* Header Decoration */}
                <div className="absolute top-0 inset-x-0 h-1.5 bg-linear-to-r from-primary to-purple-600"></div>

                <div className="p-8 pt-10">
                    <div className="flex justify-between items-start mb-8">
                        <div>
                            <h3 className="text-2xl font-black text-brand-dark tracking-tight uppercase">Reserve <span className="text-primary">Spot</span></h3>
                            <p className="text-gray-400 text-[10px] font-black uppercase tracking-widest mt-1">Available Categories</p>
                        </div>
                        <div className="p-3 bg-primary/10 rounded-2xl">
                            <TicketIcon className="w-6 h-6 text-primary" />
                        </div>
                    </div>

                    <div className="space-y-6">
                        {activeTickets.length === 0 ? (
                            <div className="text-center py-12 px-6 bg-gray-50/50 rounded-3xl border border-dashed border-gray-200">
                                <ShoppingBag className="w-10 h-10 text-gray-300 mx-auto mb-4" />
                                <p className="font-bold text-gray-400 uppercase text-[10px] tracking-widest">No tickets currently available</p>
                            </div>
                        ) : (
                            activeTickets.map(ticket => (
                                <div key={ticket.id} className="relative group/item bg-white/40 hover:bg-white/80 p-5 rounded-3xl border border-gray-100 hover:border-primary/20 transition-all duration-300">
                                    <div className="flex justify-between items-start mb-4">
                                        <div className="pr-4">
                                            <h4 className="font-black text-brand-dark uppercase tracking-tight text-sm mb-1">{ticket.name}</h4>
                                            {ticket.description.Valid && (
                                                <div
                                                    className="text-[10px] text-gray-400 font-medium leading-relaxed prose prose-sm prose-slate max-w-none 
                                                               [&_p]:mb-1 [&_p]:mt-0 [&_ul]:list-disc [&_ul]:pl-4 [&_ol]:list-decimal [&_ol]:pl-4"
                                                    dangerouslySetInnerHTML={{ __html: ticket.description.String }}
                                                />
                                            )}
                                        </div>
                                        <div className="text-right shrink-0">
                                            <span className="block font-black text-lg text-brand-dark tracking-tighter">
                                                {ticket.price === 0 ? "FREE" : `Rp ${ticket.price.toLocaleString('id-ID')}`}
                                            </span>
                                            {ticket.quota < 50 && (
                                                <span className="text-[9px] text-red-500 font-black uppercase tracking-tighter bg-red-50 px-2 py-0.5 rounded-full">
                                                    Only {ticket.quota} Left
                                                </span>
                                            )}
                                        </div>
                                    </div>

                                    <div className="flex items-center justify-between gap-4">
                                        <div className="flex-1">
                                            <div className="h-1 w-full bg-gray-100 rounded-full overflow-hidden">
                                                <div
                                                    className="h-full bg-primary transition-all duration-1000"
                                                    style={{ width: `${Math.min(100, (selection[ticket.id] || 0) * 10)}%` }}
                                                ></div>
                                            </div>
                                        </div>

                                        <div className="flex items-center gap-2 bg-brand-dark rounded-2xl p-1 shadow-lg">
                                            <button
                                                onClick={() => updateQuantity(ticket.id, -1, ticket.max_purchase_per_user)}
                                                className="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-white/10 text-white transition-all disabled:opacity-20"
                                                disabled={(selection[ticket.id] || 0) <= 0}
                                            >
                                                <Minus className="w-4 h-4" />
                                            </button>
                                            <span className="w-6 text-center text-xs font-black text-white">
                                                {selection[ticket.id] || 0}
                                            </span>
                                            <button
                                                onClick={() => updateQuantity(ticket.id, 1, ticket.max_purchase_per_user)}
                                                className="w-8 h-8 flex items-center justify-center rounded-xl hover:bg-white/10 text-primary transition-all disabled:opacity-20"
                                                disabled={(selection[ticket.id] || 0) >= ticket.max_purchase_per_user || (selection[ticket.id] || 0) >= ticket.quota}
                                            >
                                                <Plus className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ))
                        )}
                    </div>
                </div>

                {/* Footer Section - Totals & Button */}
                <div className="p-8 bg-linear-to-b from-gray-50/50 to-gray-100/80 border-t border-white">
                    <div className="flex flex-col gap-6">
                        <div className="space-y-3">
                            <div className="flex justify-between items-center">
                                <span className="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Total Purchase</span>
                                <span className="font-black text-brand-dark">{totalQty} ITEMS</span>
                            </div>
                            <div className="flex justify-between items-end">
                                <span className="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400">Investment</span>
                                <div className="text-right">
                                    <span className="block text-3xl font-black text-primary tracking-tighter">Rp {totalPrice.toLocaleString('id-ID')}</span>
                                    <span className="text-[9px] text-gray-400 font-bold uppercase tracking-widest">Pricing includes all taxes</span>
                                </div>
                            </div>
                        </div>

                        <button
                            onClick={handleCheckout}
                            disabled={totalQty === 0}
                            className="group/btn relative w-full overflow-hidden py-5 bg-brand-dark text-white rounded-3xl font-black uppercase tracking-[0.2em] text-xs shadow-2xl transition-all duration-300 hover:bg-primary hover:-translate-y-1 active:scale-95 disabled:opacity-50 disabled:hover:bg-brand-dark disabled:hover:translate-y-0 disabled:active:scale-100"
                        >
                            <span className="relative z-10 flex items-center justify-center gap-3">
                                Complete Reservation
                                <ShoppingBag className="w-4 h-4 transition-transform group-hover/btn:translate-x-1" />
                            </span>
                            <div className="absolute inset-x-0 bottom-0 h-0 group-hover/btn:h-full bg-primary transition-all duration-500 opacity-20"></div>
                        </button>

                        <div className="flex items-center justify-center gap-2">
                            <ShieldCheck className="w-4 h-4 text-green-500" />
                            <p className="text-[10px] text-gray-400 font-bold tracking-widest uppercase">
                                Encrypted Secure Checkout
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    );
}
