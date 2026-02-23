"use client";

import { useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import Cookies from "js-cookie";

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
}

export default function AdminGuard({ children }: { children: React.ReactNode }) {
    const router = useRouter();
    const [isAuthorized, setIsAuthorized] = useState<boolean | null>(null);

    useEffect(() => {
        const checkAuth = () => {
            const token = Cookies.get("token");
            const userStr = Cookies.get("user");

            if (!token || !userStr) {
                console.log("[AdminGuard] No token or user cookie found");
                setIsAuthorized(false);
                router.push("/admin/login");
                return;
            }

            try {
                const user: User = JSON.parse(userStr);
                if (user.role === "admin") {
                    setIsAuthorized(true);
                } else if (user.role === "organizer") {
                    console.log("[AdminGuard] Organizer attempting to access admin - redirecting to /organizer");
                    setIsAuthorized(false);
                    router.push("/organizer");
                } else {
                    console.log(`[AdminGuard] Unauthorized role: ${user.role} - redirecting to /`);
                    setIsAuthorized(false);
                    router.push("/");
                }
            } catch (error) {
                console.error("[AdminGuard] Error parsing user cookie", error);
                setIsAuthorized(false);
                router.push("/admin/login");
            }
        };

        checkAuth();
    }, [router]);

    if (isAuthorized === null) {
        return (
            <div className="flex items-center justify-center min-h-screen bg-(--background)">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>
        );
    }

    if (isAuthorized === false) {
        return null; // Redirecting...
    }

    return <>{children}</>;
}
