"use client";

import { useEffect } from "react";
import { useRouter } from "next/navigation";
import { toast } from "react-hot-toast";

export default function OrganizerCreateEventPage() {
    const router = useRouter();

    useEffect(() => {
        toast.error("Event creation is restricted to administrators.");
        router.replace("/organizer/events");
    }, [router]);

    return (
        <div className="flex justify-center items-center h-screen italic text-gray-400">
            Redirecting...
        </div>
    );
}
