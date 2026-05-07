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

export default function ScannerGuard({ children }: { children: React.ReactNode }) {
    const router = useRouter();
    const [isAuthorized, setIsAuthorized] = useState<boolean | null>(null);

    useEffect(() => {
        const token = Cookies.get("token");
        const userStr = Cookies.get("user");

        if (!token || !userStr) {
            setIsAuthorized(false);
            router.push("/admin/login");
            return;
        }

        try {
            const user: User = JSON.parse(userStr);
            if (user.role === "scanner") {
                setIsAuthorized(true);
            } else {
                setIsAuthorized(false);
                router.push("/");
            }
        } catch {
            setIsAuthorized(false);
            router.push("/admin/login");
        }
    }, [router]);

    if (isAuthorized === null) {
        return (
            <div className="flex items-center justify-center min-h-screen bg-gray-900">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            </div>
        );
    }

    if (!isAuthorized) return null;

    return <>{children}</>;
}
