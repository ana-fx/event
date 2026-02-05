"use client";

import Link from "next/link";
import { Ticket } from "lucide-react";

export default function Navbar() {
    return (
        <nav className="fixed top-0 left-0 right-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-100">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between items-center h-16">
                    {/* Logo */}
                    <Link href="/" className="flex items-center gap-2 group">
                        <div className="w-8 h-8 rounded-lg bg-blue-600 flex items-center justify-center transform group-hover:rotate-6 transition-transform duration-300">
                            <Ticket className="w-5 h-5 text-white" />
                        </div>
                        <span className="font-bold text-xl tracking-tight text-gray-900">Event<span className="text-blue-600">Hub</span></span>
                    </Link>

                    {/* Nav Links */}
                    <div className="hidden md:flex items-center gap-8">
                        <Link href="/" className="text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">Home</Link>
                        <Link href="/events" className="text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">Explore Events</Link>
                        <Link href="/about" className="text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">About Us</Link>
                        <Link href="/contact" className="text-sm font-medium text-gray-700 hover:text-blue-600 transition-colors">Contact</Link>
                    </div>

                    {/* CTA */}
                    <div className="flex items-center gap-4">
                        <Link href="/admin/login" className="text-sm font-bold text-gray-700 hover:text-blue-600 hidden sm:block">
                            Login
                        </Link>
                        <Link
                            href="/events"
                            className="px-4 py-2 bg-blue-600 text-white text-sm font-bold rounded-full hover:bg-blue-700 transition-all shadow-lg shadow-blue-600/20 active:scale-95"
                        >
                            Get Tickets
                        </Link>
                    </div>
                </div>
            </div>
        </nav>
    );
}
