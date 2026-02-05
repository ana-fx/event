"use client";

import { useState } from "react";
import { Copy, MapPin, Calendar, Clock, ChevronDown, ChevronUp, CheckCircle, Ticket as TicketIcon } from "lucide-react";
import { format } from "date-fns";
import toast from "react-hot-toast";

interface Ticket {
    id: number;
    name: string;
    description: { String: string; Valid: boolean };
    price: number;
    quota: number;
    max_purchase_per_user: number;
    start_date: string;
    end_date: string;
    is_active: boolean;
}

export default function TicketSelector({ tickets }: { tickets: Ticket[] }) {
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
        // Save selection to storage or context and redirect to checkout
        // For now, let's just show an alert or redirect
        // In a real app, this would likely POST to a cart API or redirect with query params
        // For parity, we might redirect to /checkout?tickets=...

        const selectedTickets = Object.entries(selection)
            .filter(([_, qty]) => qty > 0)
            .map(([id, qty]) => ({ id: Number(id), qty }));

        // Encode and redirect
        // window.location.href = `/checkout?data=${encodeURIComponent(JSON.stringify(selectedTickets))}`;
        toast.success("Proceeding to checkout...");
        // Implement Redirect logic later when Checkout page exists
    };

    const activeTickets = tickets.filter(t => t.is_active);

    return (
        <div className="bg-white rounded-3xl shadow-xl border border-gray-100 overflow-hidden sticky top-24">
            <div className="p-6 bg-gray-900 text-white">
                <h3 className="text-lg font-bold flex items-center gap-2">
                    <TicketIcon className="w-5 h-5 text-blue-400" />
                    Select Tickets
                </h3>
                <p className="text-gray-400 text-sm mt-1">Choose your preferred category</p>
            </div>

            <div className="p-6 space-y-6">
                {activeTickets.length === 0 ? (
                    <div className="text-center py-8 text-gray-500">
                        No tickets available at the moment.
                    </div>
                ) : (
                    activeTickets.map(ticket => (
                        <div key={ticket.id} className="border-b border-gray-100 last:border-0 pb-6 last:pb-0">
                            <div className="flex justify-between items-start mb-2">
                                <div>
                                    <h4 className="font-bold text-gray-900">{ticket.name}</h4>
                                    {ticket.description.Valid && (
                                        <p className="text-xs text-gray-500 mt-1 max-w-[200px]">{ticket.description.String}</p>
                                    )}
                                </div>
                                <div className="text-right">
                                    <span className="block font-black text-lg text-blue-600">
                                        {ticket.price === 0 ? "FREE" : `Rp ${ticket.price.toLocaleString('id-ID')}`}
                                    </span>
                                    <span className="text-[10px] text-gray-400 uppercase tracking-wider font-bold">
                                        {ticket.quota > 0 ? `${ticket.quota} Available` : "Sold Out"}
                                    </span>
                                </div>
                            </div>

                            <div className="flex items-center justify-between mt-4 bg-gray-50 rounded-xl p-2">
                                <span className="text-xs font-bold text-gray-500 px-2">Quantity</span>
                                <div className="flex items-center gap-3 bg-white rounded-lg shadow-sm border border-gray-200 p-1">
                                    <button
                                        onClick={() => updateQuantity(ticket.id, -1, ticket.max_purchase_per_user)}
                                        className="w-8 h-8 flex items-center justify-center rounded-md hover:bg-gray-100 text-gray-600 transition-colors disabled:opacity-50"
                                        disabled={(selection[ticket.id] || 0) <= 0}
                                    >
                                        -
                                    </button>
                                    <span className="w-8 text-center font-bold text-gray-900">
                                        {selection[ticket.id] || 0}
                                    </span>
                                    <button
                                        onClick={() => updateQuantity(ticket.id, 1, ticket.max_purchase_per_user)}
                                        className="w-8 h-8 flex items-center justify-center rounded-md hover:bg-gray-100 text-blue-600 transition-colors disabled:opacity-50"
                                        disabled={(selection[ticket.id] || 0) >= ticket.max_purchase_per_user || (selection[ticket.id] || 0) >= ticket.quota}
                                    >
                                        +
                                    </button>
                                </div>
                            </div>
                        </div>
                    ))
                )}
            </div>

            <div className="p-6 bg-gray-50 border-t border-gray-100 space-y-4">
                <div className="flex justify-between items-center text-sm">
                    <span className="text-gray-500">Total Quantity</span>
                    <span className="font-bold text-gray-900">{totalQty} Tickets</span>
                </div>
                <div className="flex justify-between items-center text-lg">
                    <span className="font-bold text-gray-900">Total Price</span>
                    <span className="font-black text-blue-600">Rp {totalPrice.toLocaleString('id-ID')}</span>
                </div>

                <button
                    onClick={handleCheckout}
                    disabled={totalQty === 0}
                    className="w-full py-4 bg-gray-900 text-white font-bold rounded-xl shadow-lg shadow-gray-900/10 hover:bg-blue-600 hover:-translate-y-1 transition-all duration-300 disabled:opacity-50 disabled:hover:bg-gray-900 disabled:hover:translate-y-0"
                >
                    Buy Tickets
                </button>
                <p className="text-[10px] text-center text-gray-400">
                    Secure payment powered by Midtrans
                </p>
            </div>
        </div>
    );
}
