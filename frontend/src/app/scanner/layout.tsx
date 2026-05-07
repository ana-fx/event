"use client";

import ScannerGuard from "@/components/auth/ScannerGuard";
import { QrCode, LayoutDashboard, LogOut } from "lucide-react";
import Cookies from "js-cookie";
import { useRouter, usePathname } from "next/navigation";
import Link from "next/link";

export default function ScannerLayout({ children }: { children: React.ReactNode }) {
    const router = useRouter();
    const pathname = usePathname();

    const handleLogout = () => {
        Cookies.remove("token");
        Cookies.remove("user");
        router.push("/admin/login");
    };

    const navItems = [
        { href: "/scanner", label: "Dashboard", icon: LayoutDashboard },
    ];

    return (
        <ScannerGuard>
            <div className="min-h-screen bg-gray-950 text-white flex flex-col">
                {/* Header */}
                <header className="bg-gray-900 border-b border-gray-800 px-4 py-3 flex items-center justify-between sticky top-0 z-10">
                    <div className="flex items-center gap-2">
                        <QrCode className="text-primary w-5 h-5" />
                        <span className="font-bold text-lg">Scanner Portal</span>
                    </div>
                    <nav className="hidden sm:flex items-center gap-1">
                        {navItems.map((item) => {
                            const Icon = item.icon;
                            const isActive = pathname === item.href;
                            return (
                                <Link
                                    key={item.href}
                                    href={item.href}
                                    className={`flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm transition-colors ${isActive ? "bg-primary text-white" : "text-gray-400 hover:text-white hover:bg-gray-800"}`}
                                >
                                    <Icon className="w-4 h-4" />
                                    {item.label}
                                </Link>
                            );
                        })}
                    </nav>
                    <button
                        onClick={handleLogout}
                        className="flex items-center gap-1.5 text-gray-400 hover:text-white text-sm transition-colors"
                    >
                        <LogOut className="w-4 h-4" />
                        <span className="hidden sm:inline">Logout</span>
                    </button>
                </header>

                {/* Main Content */}
                <main className="flex-1">
                    {children}
                </main>
            </div>
        </ScannerGuard>
    );
}
