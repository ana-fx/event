import { notFound } from "next/navigation";
import Image from "next/image";
import { Calendar, MapPin, Clock, Share2, Info } from "lucide-react";
import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import TicketSelector from "@/components/public/TicketSelector";
import { Metadata } from "next";

// Define Types (match backend response)
interface Event {
    id: number;
    name: string;
    description: string;
    start_date: string;
    end_date: string;
    location: string;
    city: string;
    banner_path: string | null;
    thumbnail_path: string | null;
    seo_title: string;
    seo_description: string;
    organizer_name: string;
    organizer_logo_path: string | null;
}

interface Ticket {
    id: number;
    event_id: number;
    name: string;
    description: { String: string; Valid: boolean };
    price: number;
    quota: number;
    max_purchase_per_user: number;
    start_date: string;
    end_date: string;
    is_active: boolean;
}

interface EventDetailResponse {
    event: Event;
    tickets: Ticket[];
}

async function getEvent(slug: string): Promise<EventDetailResponse | null> {
    try {
        const res = await fetch(`${process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080'}/api/events/detail?slug=${slug}`, {
            next: { revalidate: 0 } // Always fresh for ticket availability
        });

        if (!res.ok) return null;
        return res.json();
    } catch (error) {
        console.error("Failed to fetch event:", error);
        return null;
    }
}

export async function generateMetadata({ params }: { params: Promise<{ slug: string }> }): Promise<Metadata> {
    const slug = (await params).slug;
    const data = await getEvent(slug);

    if (!data) return { title: "Event Not Found" };

    return {
        title: `${data.event.seo_title || data.event.name} | Ingate`,
        description: data.event.seo_description || data.event.description.substring(0, 160),
        openGraph: {
            images: data.event.thumbnail_path ? [`${process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080'}${data.event.thumbnail_path}`] : [],
        }
    };
}

export default async function EventDetailPage({ params }: { params: Promise<{ slug: string }> }) {
    const slug = (await params).slug;
    const data = await getEvent(slug);

    if (!data) {
        notFound();
    }

    const { event, tickets } = data;

    // Helper for Date Formatting
    const formatDate = (dateStr: string) => {
        return new Date(dateStr).toLocaleDateString('id-ID', {
            weekday: 'long',
            day: 'numeric',
            month: 'long',
            year: 'numeric'
        });
    };

    const formatTime = (dateStr: string) => {
        return new Date(dateStr).toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
    };

    return (
        <div className="min-h-screen bg-gray-50 flex flex-col">
            <Navbar />

            {/* Hero Image / Banner */}
            <div className="relative h-[400px] md:h-[500px] w-full bg-gray-900 mt-20">
                {event.banner_path ? (
                    <Image
                        src={`${process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080'}${event.banner_path}`}
                        alt={event.name}
                        fill
                        className="object-cover opacity-80"
                        priority
                    />
                ) : ( // Fallback to thumbnail if no banner
                    event.thumbnail_path && (
                        <Image
                            src={`${process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080'}${event.thumbnail_path}`}
                            alt={event.name}
                            fill
                            className="object-cover opacity-80 blur-sm" // Blur if using thumb as banner
                        />
                    )
                )}
                <div className="absolute inset-0 bg-linear-to-t from-gray-900 via-transparent to-transparent"></div>

                {/* Content Overlay */}
                <div className="absolute bottom-0 left-0 right-0 p-6 md:p-12">
                    <div className="max-w-7xl mx-auto flex flex-col md:flex-row gap-8 items-end">
                        {/* Thumbnail Floating */}
                        <div className="hidden md:block w-48 h-64 relative rounded-2xl overflow-hidden shadow-2xl border-4 border-white shrink-0 -mb-20 z-10 bg-gray-200">
                            {event.thumbnail_path && (
                                <Image
                                    src={`${process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080'}${event.thumbnail_path}`}
                                    alt={event.name}
                                    fill
                                    className="object-cover"
                                />
                            )}
                        </div>

                        {/* Title & Info */}
                        <div className="flex-1 text-white mb-6">
                            <h1 className="text-3xl md:text-5xl font-black uppercase tracking-tight leading-tight mb-4">
                                {event.name}
                            </h1>
                            <div className="flex flex-wrap gap-4 md:gap-8 text-sm md:text-base font-medium text-gray-200">
                                <div className="flex items-center gap-2">
                                    <Calendar className="w-5 h-5 text-blue-400" />
                                    <span>{formatDate(event.start_date)}</span>
                                </div>
                                <div className="flex items-center gap-2">
                                    <Clock className="w-5 h-5 text-blue-400" />
                                    <span>{formatTime(event.start_date)} WIB</span>
                                </div>
                                <div className="flex items-center gap-2">
                                    <MapPin className="w-5 h-5 text-red-500" />
                                    <span>{event.location}, {event.city}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main className="max-w-7xl mx-auto px-6 lg:px-10 py-12 lg:py-24 w-full flex-1">
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">

                    {/* Left Column: Description & Details */}
                    <div className="lg:col-span-2 space-y-12">

                        {/* Mobile Thumbnail (visible only on small screens) */}
                        <div className="md:hidden w-full aspect-4/3 relative rounded-2xl overflow-hidden shadow-xl mb-8">
                            {event.thumbnail_path && (
                                <Image
                                    src={`${process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080'}${event.thumbnail_path}`}
                                    alt={event.name}
                                    fill
                                    className="object-cover"
                                />
                            )}
                        </div>

                        {/* Description */}
                        <div>
                            <h2 className="text-2xl font-bold text-gray-900 mb-6 flex items-center gap-3">
                                <Info className="w-6 h-6 text-blue-600" />
                                About This Event
                            </h2>
                            <div
                                className="prose prose-lg prose-blue max-w-none text-gray-600 leading-relaxed"
                                dangerouslySetInnerHTML={{ __html: event.description }}
                            />
                        </div>

                        {/* Organizer Info (Optional) */}
                        <div className="bg-white p-6 rounded-2xl border border-gray-100 flex items-center gap-4">
                            <div className="w-16 h-16 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 overflow-hidden relative">
                                {event.organizer_logo_path ? (
                                    <Image
                                        src={`${process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080'}${event.organizer_logo_path}`}
                                        alt={event.organizer_name || "Organizer"}
                                        fill
                                        className="object-cover"
                                    />
                                ) : (
                                    <span className="font-bold text-xs">ORG</span>
                                )}
                            </div>
                            <div>
                                <p className="text-xs font-bold text-gray-400 uppercase tracking-widest">Organized By</p>
                                <h3 className="text-lg font-bold text-gray-900">{event.organizer_name || "Ingate Official"}</h3>
                            </div>
                        </div>
                    </div>

                    {/* Right Column: Ticket Selector */}
                    <div className="lg:col-span-1">
                        <TicketSelector tickets={tickets} />

                        <div className="mt-8 text-center text-xs text-gray-400">
                            <p>Need help? <a href="/contact" className="text-blue-600 hover:underline">Contact Support</a></p>
                        </div>
                    </div>

                </div>
            </main>

            <Footer />
        </div>
    );
}
