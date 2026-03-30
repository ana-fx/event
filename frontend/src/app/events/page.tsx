import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import EventList from "@/components/public/EventList";
import { Search } from "lucide-react";

async function getEvents() {
    try {
        const backendUrl = process.env.BACKEND_URL;
        const publicApiUrl = process.env.NEXT_PUBLIC_API_URL;

        if (!backendUrl && !publicApiUrl) {
            console.error('[Server] ERROR: Neither BACKEND_URL nor NEXT_PUBLIC_API_URL is set!');
            return [];
        }

        let apiUrl = "";
        if (backendUrl) {
            const cleanBase = backendUrl.endsWith('/') ? backendUrl.slice(0, -1) : backendUrl;
            apiUrl = `${cleanBase}/api`;
        } else {
            apiUrl = publicApiUrl!;
        }

        console.log(`[Server] Fetching events from: ${apiUrl}/events`);

        const res = await fetch(`${apiUrl}/events`, {
            cache: "no-store",
        });

        if (!res.ok) {
            console.error(`[Server] Failed to fetch events. Status: ${res.status}`);
            return [];
        }

        return await res.json();
    } catch (error) {
        console.error("[Server] Error in getEvents:", error);
        return [];
    }
}

export default async function EventsPage() {
    const events = await getEvents();

    return (
        <div className="min-h-screen bg-gray-50 flex flex-col">
            <Navbar />

            <main className="flex-1 w-full pt-20">
                {/* Header */}
                <div className="bg-white border-b border-gray-100 py-12 px-6">
                    <div className="max-w-7xl mx-auto">
                        <h1 className="text-4xl md:text-5xl lg:text-6xl font-black text-brand-dark mb-6 tracking-tighter uppercase font-heading">Explore Events</h1>
                        <p className="text-gray-500 max-w-2xl font-medium font-body">Discover the best concerts, workshops, and gatherings happening around you.</p>

                        {/* Search Bar Placeholder (Functional search requires client component wrapping) */}
                        <div className="mt-8 relative max-w-lg hidden">
                            <input
                                type="text"
                                placeholder="Search events..."
                                className="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-primary outline-none transition-all"
                            />
                            <Search className="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 w-5 h-5" />
                        </div>
                    </div>
                </div>

                {/* List */}
                <div className="max-w-7xl mx-auto px-6 py-12">
                    <EventList initialEvents={events} serverNow={Date.now()} />
                </div>
            </main>

            <Footer />
        </div>
    );
}
