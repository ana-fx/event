"use client";

import { useEffect, useState } from "react";
import Cookies from "js-cookie";
import { Menu, Sun, Moon } from "lucide-react";
import { useSidebar } from "@/context/SidebarContext";
import { useTheme } from "@/context/ThemeContext";

export default function Header() {
    const [user, setUser] = useState<{ name: string, email: string } | null>(null);
    const { toggle } = useSidebar();
    const { theme, toggleTheme } = useTheme();

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
        <header className="fixed top-0 right-0 left-0 md:left-72 h-16 bg-(--card)/80 backdrop-blur-xl border-b border-(--card-border) z-40 flex items-center justify-between px-6">
            {/* Search or Breadcrumbs placeholder */}
            <div className="flex items-center gap-4">
                <button
                    onClick={toggle}
                    className="p-2 -ml-2 text-gray-600 hover:bg-gray-100 rounded-lg md:hidden"
                >
                    <Menu className="w-6 h-6" />
                </button>
                <h2 className="text-xl font-bold text-(--foreground)">Overview</h2>
            </div>

            <div className="flex items-center gap-6">
                {/* Notifications Placeholder */}
                <button
                    onClick={toggleTheme}
                    className="p-2.5 rounded-xl bg-(--background) border border-(--card-border) text-gray-500 hover:text-blue-600 transition-all shadow-sm"
                    title={theme === 'light' ? 'Switch to Dark Mode' : 'Switch to Light Mode'}
                >
                    {theme === 'light' ? <Moon className="h-5 w-5" /> : <Sun className="h-5 w-5" />}
                </button>

                <div className="w-px h-6 bg-(--card-border)" />

                <div className="flex items-center gap-4 pl-2 cursor-pointer group">
                    <div className="text-right hidden sm:block">
                        <p className="text-sm font-bold text-(--foreground) group-hover:text-blue-600 transition-colors">{user?.name || "Admin"}</p>
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
