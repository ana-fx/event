"use client";

import ScannerGuard from "@/components/auth/ScannerGuard";
import { QrCode, LayoutDashboard, ClipboardList, LogOut, Menu, X } from "lucide-react";
import Cookies from "js-cookie";
import { useRouter, usePathname } from "next/navigation";
import Link from "next/link";
import { useState } from "react";

const navItems = [
    { href: "/scanner", label: "Dashboard", icon: LayoutDashboard, exact: true },
    { href: "/scanner/report", label: "Scan Report", icon: ClipboardList, exact: false },
];

export default function ScannerLayout({ children }: { children: React.ReactNode }) {
    const router = useRouter();
    const pathname = usePathname();
    const [mobileOpen, setMobileOpen] = useState(false);

    const handleLogout = () => {
        Cookies.remove("token");
        Cookies.remove("user");
        router.push("/admin/login");
    };

    const isActive = (item: typeof navItems[0]) =>
        item.exact ? pathname === item.href : pathname.startsWith(item.href);

    return (
        <ScannerGuard>
            <div className="min-h-screen bg-gray-950 text-white flex">

                {/* Sidebar — desktop */}
                <aside className="hidden md:flex flex-col w-60 bg-gray-900 border-r border-gray-800 fixed inset-y-0 left-0 z-20">
                    {/* Logo */}
                    <div className="flex items-center gap-2.5 px-5 py-5 border-b border-gray-800">
                        <div className="w-8 h-8 bg-primary rounded-lg flex items-center justify-center shrink-0">
                            <QrCode className="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <p className="font-bold text-white text-sm leading-none">Scanner</p>
                            <p className="text-[10px] text-gray-500 mt-0.5">Portal</p>
                        </div>
                    </div>

                    {/* Nav */}
                    <nav className="flex-1 px-3 py-4 space-y-1">
                        {navItems.map((item) => {
                            const Icon = item.icon;
                            const active = isActive(item);
                            return (
                                <Link
                                    key={item.href}
                                    href={item.href}
                                    className={`flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors ${
                                        active
                                            ? "bg-primary text-white"
                                            : "text-gray-400 hover:text-white hover:bg-gray-800"
                                    }`}
                                >
                                    <Icon className="w-4 h-4 shrink-0" />
                                    {item.label}
                                </Link>
                            );
                        })}
                    </nav>

                    {/* Logout */}
                    <div className="px-3 py-4 border-t border-gray-800">
                        <button
                            onClick={handleLogout}
                            className="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 transition-colors"
                        >
                            <LogOut className="w-4 h-4 shrink-0" />
                            Logout
                        </button>
                    </div>
                </aside>

                {/* Mobile header */}
                <header className="md:hidden fixed top-0 inset-x-0 z-20 bg-gray-900 border-b border-gray-800 flex items-center justify-between px-4 py-3">
                    <div className="flex items-center gap-2">
                        <div className="w-7 h-7 bg-primary rounded-lg flex items-center justify-center">
                            <QrCode className="w-3.5 h-3.5 text-white" />
                        </div>
                        <span className="font-bold text-sm">Scanner Portal</span>
                    </div>
                    <button onClick={() => setMobileOpen(p => !p)} className="text-gray-400 hover:text-white transition-colors">
                        {mobileOpen ? <X className="w-5 h-5" /> : <Menu className="w-5 h-5" />}
                    </button>
                </header>

                {/* Mobile drawer */}
                {mobileOpen && (
                    <div className="md:hidden fixed inset-0 z-10 bg-black/60" onClick={() => setMobileOpen(false)}>
                        <aside className="w-60 bg-gray-900 h-full border-r border-gray-800 flex flex-col" onClick={e => e.stopPropagation()}>
                            <div className="flex items-center gap-2.5 px-5 py-5 border-b border-gray-800">
                                <div className="w-8 h-8 bg-primary rounded-lg flex items-center justify-center">
                                    <QrCode className="w-4 h-4 text-white" />
                                </div>
                                <div>
                                    <p className="font-bold text-white text-sm leading-none">Scanner</p>
                                    <p className="text-[10px] text-gray-500 mt-0.5">Portal</p>
                                </div>
                            </div>
                            <nav className="flex-1 px-3 py-4 space-y-1">
                                {navItems.map((item) => {
                                    const Icon = item.icon;
                                    const active = isActive(item);
                                    return (
                                        <Link
                                            key={item.href}
                                            href={item.href}
                                            onClick={() => setMobileOpen(false)}
                                            className={`flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-medium transition-colors ${
                                                active
                                                    ? "bg-primary text-white"
                                                    : "text-gray-400 hover:text-white hover:bg-gray-800"
                                            }`}
                                        >
                                            <Icon className="w-4 h-4 shrink-0" />
                                            {item.label}
                                        </Link>
                                    );
                                })}
                            </nav>
                            <div className="px-3 py-4 border-t border-gray-800">
                                <button
                                    onClick={handleLogout}
                                    className="flex items-center gap-3 w-full px-3 py-2.5 rounded-xl text-sm font-medium text-gray-400 hover:text-white hover:bg-gray-800 transition-colors"
                                >
                                    <LogOut className="w-4 h-4 shrink-0" />
                                    Logout
                                </button>
                            </div>
                        </aside>
                    </div>
                )}

                {/* Content area */}
                <main className="flex-1 md:ml-60 pt-14 md:pt-0 min-h-screen">
                    {children}
                </main>
            </div>
        </ScannerGuard>
    );
}
