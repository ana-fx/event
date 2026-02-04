"use client";

import { useEffect, useState } from "react";
import Cookies from "js-cookie";

export default function Header() {
    const [user, setUser] = useState<{ name: string, email: string } | null>(null);

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
        <header className="h-16 bg-white border-b border-gray-100 fixed top-0 right-0 left-64 z-40 px-8 flex items-center justify-end">
            <div className="flex items-center gap-4">
                <div className="text-right hidden sm:block">
                    <p className="text-sm font-bold text-gray-900">{user?.name || "Admin"}</p>
                    <p className="text-xs text-gray-500">{user?.email || "admin@example.com"}</p>
                </div>
                <div className="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-700 font-bold border border-blue-200">
                    {user?.name?.[0].toUpperCase() || "A"}
                </div>
            </div>
        </header>
    );
}
