"use client";

import Link from "next/link";
import Image from "next/image";
import { Ticket, Menu, X } from "lucide-react";
import { useState } from "react";

export default function Navbar() {
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);

    return (
        <nav className="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-2xl border-b border-gray-100/50">
            <div className="max-w-7xl mx-auto px-6 lg:px-10">
                <div className="flex justify-between items-center h-24">
                    {/* Logo */}
                    <Link href="/" className="flex items-center group">
                        <Image
                            src="/logo-ingate.png"
                            alt="Ingate Logo"
                            width={160}
                            height={48}
                            className="h-10 md:h-12 w-auto object-contain"
                            priority
                        />
                    </Link>

                    {/* Desktop Editorial Navigation */}
                    <div className="hidden md:flex items-center gap-12">
                        {[
                            { label: 'Home', path: '/' },
                            { label: 'Events', path: '/events' },
                            { label: 'Contact', path: '/contact' }
                        ].map((item) => (
                            <Link
                                key={item.label}
                                href={item.path}
                                className="text-[10px] font-black uppercase tracking-[0.5em] text-gray-400 hover:text-brand-dark transition-all duration-500 relative group"
                            >
                                {item.label}
                                <span className="absolute -bottom-2 left-0 w-0 h-1 bg-primary rounded-full transition-all duration-500 group-hover:w-full"></span>
                            </Link>
                        ))}
                    </div>

                    {/* Auth Area & Mobile Toggle */}
                    <div className="flex items-center gap-6">
                        <Link href="/admin/login" className="hidden md:flex items-center justify-center px-8 py-4 bg-brand-dark text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-full hover:bg-primary transition-all duration-500 shadow-2xl shadow-brand-dark/20 hover:-translate-y-1">
                            Login
                        </Link>

                        <button
                            onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
                            className="md:hidden p-3 bg-gray-50 rounded-xl text-brand-dark"
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
                        { label: 'Contact', path: '/contact' }
                    ].map((item) => (
                        <Link
                            key={item.label}
                            href={item.path}
                            onClick={() => setMobileMenuOpen(false)}
                            className="block text-2xl font-black text-brand-dark tracking-tighter hover:text-primary transition-colors"
                        >
                            {item.label}<span className="text-primary">.</span>
                        </Link>
                    ))}
                    <div className="pt-6 border-t border-gray-100">
                        <Link
                            href="/admin/login"
                            onClick={() => setMobileMenuOpen(false)}
                            className="block w-full py-4 bg-brand-dark text-white text-center font-black rounded-2xl tracking-widest text-sm uppercase"
                        >
                            Login
                        </Link>
                    </div>
                </div>
            )}
        </nav>
    );
}


