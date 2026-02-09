"use client";

import Link from "next/link";
import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import { CheckCircle, ArrowRight, Calendar, MapPin, Download } from "lucide-react";

export default function SuccessPage() {
    return (
        <div className="min-h-screen bg-white flex flex-col">
            <Navbar />

            <main className="flex-1 flex flex-col items-center justify-center px-6 py-32">
                <div className="max-w-2xl w-full text-center space-y-12">
                    
                    {/* Success Icon */}
                    <div className="relative inline-block">
                        <div className="absolute inset-0 bg-green-500 blur-3xl opacity-20 animate-pulse"></div>
                        <div className="relative w-24 h-24 rounded-[32px] bg-green-500 flex items-center justify-center shadow-2xl shadow-green-500/20">
                            <CheckCircle className="w-12 h-12 text-white" />
                        </div>
                    </div>

                    {/* Content */}
                    <div className="space-y-6">
                        <span className="text-green-600 text-[10px] font-black uppercase tracking-[0.4em] block font-heading">Reservation Confirmed</span>
                        <h1 className="text-4xl md:text-5xl lg:text-6xl font-black text-gray-950 tracking-tighter uppercase leading-none font-heading">
                            You&apos;re <span className="text-gray-300">Ready</span> <br /> to Experience.
                        </h1>
                        <p className="text-gray-500 font-medium max-w-lg mx-auto font-body">
                            Your payment has been processed successfully. We&apos;ve sent your e-tickets and confirmation details to your registered email address.
                        </p>
                    </div>

                    {/* Action Cards */}
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4 text-left">
                        <div className="p-8 rounded-[32px] bg-gray-50 border border-gray-100 space-y-4">
                            <div className="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm">
                                <Download className="w-5 h-5 text-blue-600" />
                            </div>
                            <div>
                                <h3 className="text-sm font-black uppercase tracking-tight text-gray-900 font-heading">E-Ticket Ready</h3>
                                <p className="text-[11px] text-gray-400 font-bold uppercase tracking-widest mt-1 font-body">Available in your email</p>
                            </div>
                        </div>
                        <div className="p-8 rounded-[32px] bg-gray-50 border border-gray-100 space-y-4">
                            <div className="w-10 h-10 rounded-xl bg-white flex items-center justify-center shadow-sm">
                                <Calendar className="w-5 h-5 text-purple-600" />
                            </div>
                            <div>
                                <h3 className="text-sm font-black uppercase tracking-tight text-gray-900 font-heading">Add to Calendar</h3>
                                <p className="text-[11px] text-gray-400 font-bold uppercase tracking-widest mt-1 font-body">Don&apos;t miss the date</p>
                            </div>
                        </div>
                    </div>

                    {/* Primary Button */}
                    <div className="pt-8">
                        <Link 
                            href="/events"
                            className="inline-flex items-center gap-4 px-12 py-6 bg-gray-950 text-white text-xs font-black uppercase tracking-[0.2em] rounded-[28px] hover:bg-blue-600 hover:-translate-y-1 transition-all shadow-2xl shadow-gray-950/20 group font-heading"
                        >
                            Explore More Events
                            <ArrowRight className="w-5 h-5 group-hover:translate-x-1 transition-transform" />
                        </Link>
                    </div>

                    <div className="pt-8 border-t border-gray-50 flex items-center justify-center gap-8">
                        <Link href="/" className="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-950 transition-colors font-heading">Home</Link>
                        <Link href="/contact" className="text-[10px] font-black uppercase tracking-widest text-gray-400 hover:text-gray-950 transition-colors font-heading">Support</Link>
                    </div>

                </div>
            </main>

            <Footer />
        </div>
    );
}
