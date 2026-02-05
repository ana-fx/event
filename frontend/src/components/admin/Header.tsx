"use client";

import { useEffect, useState } from "react";
import Cookies from "js-cookie";
import { Menu } from "lucide-react";
import { useSidebar } from "@/context/SidebarContext";

export default function Header() {
    const [user, setUser] = useState<{ name: string, email: string } | null>(null);
    const { toggle } = useSidebar();

    useEffect(() => {
        const userCookie = Cookies.get("user");
        if (userCookie) {
            try {
                setUser(JSON.parse(userCookie));
            } catch (e) {
                console.error("Failed to parse user cookie", e);
            }
        }
    }, []);

    return (
        <header className="h-20 fixed top-0 right-0 left-0 md:left-72 z-40 px-4 md:px-8 flex items-center justify-between bg-white/80 backdrop-blur-md border-b border-gray-100 transition-all duration-300">
            {/* Search or Breadcrumbs placeholder */}
            <div className="flex items-center gap-4">
                <button
                    onClick={toggle}
                    className="p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-lg md:hidden"
                >
                    <Menu className="w-6 h-6" />
                </button>
                <h2 className="text-xl font-bold text-gray-800">Overview</h2>
            </div>

            <div className="flex items-center gap-6">
                {/* Notifications Placeholder */}
                <button className="relative p-2 text-gray-500 hover:text-blue-600 transition-colors">
                    <div className="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full animate-ping" />
                    <div className="absolute top-2 right-2 w-2 h-2 bg-red-500 rounded-full" />
                    <svg xmlns="http://www.w3.org/2000/svg" className="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                    </svg>
                </button>

                <div className="h-8 w-px bg-gray-200" />

                <div className="flex items-center gap-4 pl-2 cursor-pointer group">
                    <div className="text-right hidden sm:block">
                        <p className="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{user?.name || "Admin"}</p>
                        <p className="text-xs text-gray-500">{user?.email || "admin@event.com"}</p>
                    </div>
                    <div className="w-11 h-11 rounded-xl bg-linear-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-bold shadow-lg shadow-blue-500/30 ring-2 ring-white group-hover:scale-105 transition-transform">
                        {user?.name?.[0].toUpperCase() || "A"}
                    </div>
                </div>
            </div>
        </header>
    );
}
