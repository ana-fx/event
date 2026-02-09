"use client";

import React from 'react';

interface MobileBookingBarProps {
    minPrice: number;
}

export default function MobileBookingBar({ minPrice }: MobileBookingBarProps) {
    const handleScrollToBooking = () => {
        const section = document.getElementById('booking-section');
        if (section) {
            section.scrollIntoView({ behavior: 'smooth' });
        }
    };

    return (
        <div className="fixed bottom-0 left-0 right-0 z-40 md:hidden bg-white/95 backdrop-blur-xl border-t border-gray-100 p-4 pb-8 flex items-center justify-between shadow-[0_-10px_40px_rgba(0,0,0,0.05)]">
            <div>
                <span className="text-[10px] font-black uppercase tracking-wider text-gray-400 block">Starts from</span>
                <span className="text-xl font-black text-gray-900 tracking-tighter">
                    {minPrice > 0 ? `Rp ${minPrice.toLocaleString('id-ID')}` : "FREE"}
                </span>
            </div>
            <button 
                onClick={handleScrollToBooking}
                className="px-8 py-4 bg-gray-950 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-gray-950/20 active:scale-95 transition-all font-heading"
            >
                Book Now
            </button>
        </div>
    );
}
