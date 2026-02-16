import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import EventList from "@/components/public/EventList";
import { Search } from "lucide-react";

async function getEvents() {
    try {
        const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080/api';
        const res = await fetch(`${apiUrl}/events`, {
            cache: "no-store",
            // next: { revalidate: 0 } // Alternative for App Router caching
        });

        if (!res.ok) {
            console.error(`API Error: ${res.status} ${res.statusText}`);
            return [];
        }

        const data = await res.json();
        console.log(`Fetched ${data?.length || 0} events from ${apiUrl}/events`);
        return data;
    } catch (error) {
        console.error("Failed to fetch events:", error);
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
                        <h1 className="text-4xl md:text-5xl lg:text-6xl font-black text-gray-950 mb-6 tracking-tighter uppercase font-heading">Explore Events</h1>
                        <p className="text-gray-500 max-w-2xl font-medium font-body">Discover the best concerts, workshops, and gatherings happening around you.</p>

                        {/* Search Bar Placeholder (Functional search requires client component wrapping) */}
                        <div className="mt-8 relative max-w-lg hidden">
                            <input
                                type="text"
                                placeholder="Search events..."
                                className="w-full pl-12 pr-4 py-3 rounded-xl border border-gray-200 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-blue-500 outline-none transition-all"
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
