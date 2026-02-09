"use client";

import Link from "next/link";
import { Ticket, Menu, X } from "lucide-react";
import { useState } from "react";

export default function Navbar() {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    return (
        <nav className="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-2xl border-b border-gray-100/50">
            <div className="max-w-7xl mx-auto px-6 lg:px-10">
                <div className="flex justify-between items-center h-24">
                    {/* Logo */}
                    <Link href="/" className="flex items-center gap-2 md:gap-3 group">
                        <div className="w-10 h-10 md:w-14 md:h-14 rounded-xl md:rounded-2xl bg-gray-950 flex items-center justify-center transform group-hover:rotate-[10deg] transition-all duration-500 shadow-xl shadow-gray-950/20">
                            <Ticket className="w-5 h-5 md:w-7 md:h-7 text-white" />
                        </div>
                        <span className="text-xl md:text-3xl font-black tracking-tighter uppercase flex items-center gap-1">
                            <span className="text-blue-600">IN</span>GATE
                        </span>
                    </Link>

                    {/* Desktop Editorial Navigation */}
                    <div className="hidden md:flex items-center gap-12">
                        {[
                            { label: 'Home', path: '/' },
                            { label: 'Events', path: '/events' },
                            { label: 'About', path: '/about-us' },
                            { label: 'Contact', path: '/contact' }
                        ].map((item) => (
                            <Link
                                key={item.label}
                                href={item.path}
                                className="text-[10px] font-black uppercase tracking-[0.5em] text-gray-400 hover:text-gray-950 transition-all duration-500 relative group"
                            >
                                {item.label}
                                <span className="absolute -bottom-2 left-0 w-0 h-1 bg-blue-600 rounded-full transition-all duration-500 group-hover:w-full"></span>
                            </Link>
                        ))}
                    </div>

                    {/* Auth Area & Mobile Toggle */}
                    <div className="flex items-center gap-6">
                        <Link href="/admin/login" className="hidden md:flex items-center justify-center px-8 py-4 bg-gray-950 text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-full hover:bg-blue-600 transition-all duration-500 shadow-2xl shadow-gray-950/20 hover:-translate-y-1">
                            Login
                        </Link>

                        <button
                            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                            className="md:hidden p-3 bg-gray-50 rounded-xl text-gray-900"
                        >
                            {mobileMenuOpen ? <X /> : <Menu />}
                        </button>
                    </div>
                </div>
            </div>

            {/* Mobile Menu Overlay */}
            {mobileMenuOpen && (
                <div className="md:hidden bg-white border-b border-gray-100 p-8 space-y-6 animate-in slide-in-from-top duration-300">
                    {[
                        { label: 'Home', path: '/' },
                        { label: 'Events', path: '/events' },
                        { label: 'About Us', path: '/about-us' },
                        { label: 'Contact', path: '/contact' }
                    ].map((item) => (
                        <Link
                            key={item.label}
                            href={item.path}
                            onClick={() => setMobileMenuOpen(false)}
                            className="block text-2xl font-black text-gray-900 tracking-tighter hover:text-blue-600 transition-colors"
                        >
                            {item.label}<span className="text-blue-600">.</span>
                        </Link>
                    ))}
                    <div className="pt-6 border-t border-gray-100">
                        <Link
                            href="/admin/login"
                            onClick={() => setMobileMenuOpen(false)}
                            className="block w-full py-4 bg-gray-900 text-white text-center font-black rounded-2xl tracking-widest text-sm uppercase"
                        >
                            Login
                        </Link>
                    </div>
                </div>
            )}
        </nav>
    );
}


