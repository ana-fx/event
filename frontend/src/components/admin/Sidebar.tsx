"use client";

import { useState } from "react";
import Link from "next/link";
import Image from "next/image";
import { useRouter, usePathname, useSearchParams } from "next/navigation";
import {
    LayoutDashboard,
    Users,
    Calendar,
    BarChart3,
    Settings,
    LogOut,
    Ticket,
    Image as ImageIcon,
    MessageSquare,
    CreditCard,
    ChevronRight,
    ChevronDown,
    Shield,
    ScanBarcode,
    Store,
    X
} from "lucide-react";
import { cn } from "@/lib/utils";
import Cookies from "js-cookie";
import { useSidebar } from "@/context/SidebarContext";

interface MenuItem {
    name: string;
    href: string;
    icon: any;
    children?: { name: string; href: string; icon?: any }[];
}

const menuItems: MenuItem[] = [
    { name: "Dashboard", href: "/admin", icon: LayoutDashboard },
    { name: "Events", href: "/admin/events", icon: Calendar },
    {
        name: "Management Role",
        href: "#",
        icon: Users,
        children: [
            { name: "Admins", href: "/admin/users?role=admin", icon: Shield },
            { name: "Organizers", href: "/admin/organizers", icon: Store },
            { name: "Scanners", href: "/admin/users?role=scanner", icon: ScanBarcode },
        ]
    },
    { name: "Banners", href: "/admin/banners", icon: ImageIcon },
    { name: "Contacts", href: "/admin/contacts", icon: MessageSquare },
    { name: "Reports", href: "/admin/reports", icon: BarChart3 },
    { name: "Settings", href: "/admin/settings", icon: Settings },
];

export default function Sidebar() {
    const pathname = usePathname();
    const searchParams = useSearchParams();
    const router = useRouter();
    const [openMenus, setOpenMenus] = useState<string[]>(["Management Role"]); // updated default
    const { isOpen, close } = useSidebar(); // Consume context

    // Close sidebar when route changes on mobile
    // Note: You might want to useEffect on pathname change

    const toggleMenu = (name: string) => {
        setOpenMenus(prev =>
            prev.includes(name) ? prev.filter(item => item !== name) : [...prev, name]
        );
    };

    const handleLogout = () => {
        Cookies.remove("token");
        Cookies.remove("user");
        router.push("/admin/login");
    };

    return (
        <>
            {/* Mobile Overlay */}
            {isOpen && (
                <div
                    className="fixed inset-0 bg-black/50 z-40 md:hidden backdrop-blur-sm transition-opacity"
                    onClick={close}
                />
            )}

            <aside className={`fixed inset-y-0 left-0 z-50 w-72 bg-(--card) text-(--foreground) border-r border-(--card-border) transition-transform duration-300 ease-in-out md:translate-x-0 ${isOpen ? 'translate-x-0' : '-translate-x-full'}`}>
                {/* Logo Section */}
                <div className="p-8 pb-4 flex justify-between items-center">
                    <Link href="/admin" className="flex items-center gap-3">
                        <Image
                            src="/logo-ingate.png"
                            alt="Ingate Logo"
                            width={120}
                            height={36}
                            className="h-8 w-auto object-contain"
                            priority
                        />
                    </Link>
                    {/* Close button for mobile */}
                    <button
                        onClick={close}
                        className="md:hidden text-gray-400 hover:text-white transition-colors"
                        aria-label="Close sidebar"
                    >
                        <X className="w-6 h-6" />
                    </button>
                </div>

                <div className="mx-8 h-px bg-(--card-border) mb-6" />

                {/* Navigation */}
                <nav className="flex-1 px-4 space-y-2 overflow-y-auto no-scrollbar">
                    <p className="px-4 text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Main Menu</p>
                    {menuItems.map((item) => {
                        const hasChildren = item.children && item.children.length > 0;
                        const isActive = pathname === item.href || (hasChildren && item.children?.some(child => pathname === child.href.split('?')[0]));
                        const isOpenMenu = openMenus.includes(item.name);

                        if (hasChildren) {
                            return (
                                <div key={item.name} className="space-y-1">
                                    <button
                                        onClick={() => toggleMenu(item.name)}
                                        className={cn(
                                            "w-full flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all duration-300 group relative overflow-hidden text-left",
                                            isActive || isOpenMenu ? "text-primary dark:text-primary bg-primary/10 dark:bg-primary/20" : "text-gray-500 hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-800/50"
                                        )}
                                    >
                                        <item.icon className={cn("w-5 h-5 transition-transform duration-300", isActive ? "text-primary" : "text-gray-500 group-hover:text-white")} />
                                        <span className="flex-1 font-medium">{item.name}</span>
                                        <ChevronDown className={cn("w-4 h-4 transition-transform duration-300", isOpenMenu ? "rotate-180" : "")} />
                                    </button>

                                    {isOpenMenu && (
                                        <div className="pl-4 space-y-1 animate-in slide-in-from-top-2 duration-200">
                                            {item.children?.map(child => {
                                                const [childPath, childQuery] = child.href.split('?');
                                                let isChildActive = pathname === childPath;

                                                if (isChildActive && childQuery) {
                                                    const params = new URLSearchParams(childQuery);
                                                    params.forEach((value, key) => {
                                                        if (searchParams.get(key) !== value) {
                                                            isChildActive = false;
                                                        }
                                                    });
                                                }

                                                return (
                                                    <Link
                                                        key={child.name}
                                                        href={child.href}
                                                        onClick={() => close()} // Close on mobile click
                                                        className={cn(
                                                            "flex items-center gap-3 px-4 py-2.5 rounded-xl transition-all duration-300 text-sm",
                                                            "text-gray-500 hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-800/30",
                                                            isChildActive ? "text-primary dark:text-primary font-semibold bg-white dark:bg-gray-800 shadow-sm" : ""
                                                        )}
                                                    >
                                                        {child.icon ? <child.icon className="w-4 h-4" /> : <div className="w-1.5 h-1.5 rounded-full bg-gray-600" />}
                                                        <span>{child.name}</span>
                                                    </Link>
                                                )
                                            })}
                                        </div>
                                    )}
                                </div>
                            );
                        }

                        return (
                            <Link
                                key={item.href}
                                href={item.href}
                                onClick={() => close()} // Close on mobile click
                                className={cn(
                                    "flex items-center gap-3 px-4 py-3.5 rounded-2xl transition-all duration-300 group relative overflow-hidden",
                                    isActive
                                        ? "bg-primary/10 text-primary dark:text-primary font-semibold shadow-[0_0_20px_rgba(37,99,235,0.05)]"
                                        : "text-gray-500 hover:text-primary hover:bg-gray-100 dark:hover:bg-gray-800/50"
                                )}
                            >
                                {isActive && <div className="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-8 bg-primary rounded-r-full" />}
                                <item.icon className={cn("w-5 h-5 transition-transform group-hover:scale-110 duration-300", isActive ? "text-primary" : "text-gray-500 group-hover:text-white")} />
                                <span className="flex-1">{item.name}</span>
                                {isActive && <ChevronRight className="w-4 h-4 text-primary animate-pulse" />}
                            </Link>
                        );
                    })}
                </nav>

                {/* Logout Section */}
                <div className="p-4 mt-auto">
                    <div className="bg-(--background) rounded-2xl p-4 border border-(--card-border) backdrop-blur-sm">
                        <button
                            onClick={handleLogout}
                            className="flex items-center gap-3 w-full text-gray-400 hover:text-red-500 transition-all group px-2 py-1.5 rounded-xl hover:bg-red-500/5"
                        >
                            <div className="w-8 h-8 rounded-lg bg-(--card) flex items-center justify-center group-hover:bg-red-500/10 transition-colors border border-(--card-border)">
                                <LogOut className="w-4 h-4" />
                            </div>
                            <span className="font-bold text-sm">Sign Out</span>
                        </button>
                    </div>
                </div>
            </aside>
        </>
    );
}
