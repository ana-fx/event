import { notFound } from "next/navigation";
import Image from "next/image";
import { Calendar, MapPin, Clock, Share2, Info, ChevronLeft, Globe } from "lucide-react";
import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import TicketSelector from "@/components/public/TicketSelector";
import { Metadata } from "next";
import { getImageUrl } from "@/lib/utils";
import Link from "next/link";

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
        const apiUrl = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080/api';
        const res = await fetch(`${apiUrl}/events/detail?slug=${slug}`, {
            next: { revalidate: 0 }
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
        description: data.event.seo_description || data.event.description.replace(/<[^>]*>/g, '').substring(0, 160),
        openGraph: {
            images: data.event.thumbnail_path ? [getImageUrl(data.event.thumbnail_path)] : [],
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
        <div className="min-h-screen bg-white flex flex-col font-['Outfit']">
            <Navbar />

            {/* Back Button */}
            <div className="fixed top-24 left-6 md:left-10 z-40">
                <Link
                    href="/events"
                    className="flex items-center gap-2 px-4 py-2 bg-white/80 backdrop-blur-md border border-gray-100 rounded-full text-xs font-black uppercase tracking-widest text-gray-900 hover:bg-gray-900 hover:text-white transition-all shadow-sm"
                >
                    <ChevronLeft className="w-4 h-4" />
                    Back
                </Link>
            </div>

            {/* Premium Hero Section */}
            <div className="relative h-[60vh] md:h-[80vh] w-full bg-gray-900 overflow-hidden flex items-end">
                {event.banner_path ? (
                    <Image
                        src={getImageUrl(event.banner_path)}
                        alt={event.name}
                        fill
                        className="object-cover opacity-60 scale-105"
                        priority
                    />
                ) : (
                    event.thumbnail_path && (
                        <Image
                            src={getImageUrl(event.thumbnail_path)}
                            alt={event.name}
                            fill
                            className="object-cover opacity-40 blur-xl scale-110"
                        />
                    )
                )}

                {/* Dynamic Gradient Overlay */}
                <div className="absolute inset-0 bg-linear-to-t from-white via-white/20 to-transparent"></div>
                <div className="absolute inset-x-0 bottom-0 h-64 bg-linear-to-t from-white to-transparent"></div>

                <div className="relative w-full max-w-7xl mx-auto px-6 lg:px-10 pb-12 animate-fade-in">
                    <div className="flex flex-col gap-6">
                        <div className="flex items-center gap-3">
                            <span className="px-4 py-1.5 bg-blue-600 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full shadow-lg shadow-blue-600/20">
                                Upcoming Event
                            </span>
                            <span className="px-4 py-1.5 bg-white/10 backdrop-blur-md border border-white/20 text-white text-[10px] font-black uppercase tracking-[0.2em] rounded-full">
                                {event.city}
                            </span>
                        </div>

                        <h1 className="text-5xl md:text-8xl font-black text-gray-900 leading-tight tracking-tighter uppercase max-w-5xl">
                            {event.name}
                        </h1>

                        <div className="flex flex-wrap gap-10 items-center border-t border-gray-100 pt-8 mt-4">
                            <div className="flex items-center gap-4">
                                <div className="w-12 h-12 rounded-2xl bg-blue-50 flex items-center justify-center">
                                    <Calendar className="w-5 h-5 text-blue-600" />
                                </div>
                                <div className="flex flex-col">
                                    <span className="text-[10px] font-black uppercase tracking-widest text-gray-400">Date</span>
                                    <span className="font-bold text-gray-900">{formatDate(event.start_date)}</span>
                                </div>
                            </div>

                            <div className="flex items-center gap-4">
                                <div className="w-12 h-12 rounded-2xl bg-purple-50 flex items-center justify-center">
                                    <Clock className="w-5 h-5 text-purple-600" />
                                </div>
                                <div className="flex flex-col">
                                    <span className="text-[10px] font-black uppercase tracking-widest text-gray-400">Time</span>
                                    <span className="font-bold text-gray-900">{formatTime(event.start_date)} WIB</span>
                                </div>
                            </div>

                            <div className="flex items-center gap-4">
                                <div className="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">
                                    <MapPin className="w-5 h-5 text-red-600" />
                                </div>
                                <div className="flex flex-col">
                                    <span className="text-[10px] font-black uppercase tracking-widest text-gray-400">Location</span>
                                    <span className="font-bold text-gray-900 line-clamp-1">{event.location}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <main className="max-w-7xl mx-auto px-6 lg:px-10 py-20 w-full flex-1">
                <div className="flex flex-col lg:flex-row gap-20">

                    {/* Editorial Content Column */}
                    <div className="flex-1 space-y-20">
                        {/* Event Intro Panel */}
                        <div className="relative group">
                            <div className="prose prose-2xl prose-blue max-w-none text-gray-600 font-medium leading-[1.6] first-letter:text-7xl first-letter:font-black first-letter:text-blue-600 first-letter:mr-4 first-letter:float-left"
                                dangerouslySetInnerHTML={{ __html: event.description }}
                            />
                        </div>

                        {/* Organizer Section */}
                        <div className="p-10 rounded-[40px] bg-gray-50 border border-gray-100 flex flex-col md:flex-row gap-8 items-center">
                            <div className="w-32 h-32 rounded-[32px] bg-white p-2 shadow-2xl relative overflow-hidden flex items-center justify-center">
                                {event.organizer_logo_path ? (
                                    <Image
                                        src={getImageUrl(event.organizer_logo_path)}
                                        alt={event.organizer_name}
                                        fill
                                        className="object-cover p-2"
                                    />
                                ) : (
                                    <Globe className="w-12 h-12 text-gray-100" />
                                )}
                            </div>
                            <div className="flex-1 text-center md:text-left">
                                <span className="text-[10px] font-black uppercase tracking-[0.3em] text-blue-600 mb-2 block">Our Trusted Partner</span>
                                <h3 className="text-3xl font-black text-gray-900 tracking-tight uppercase mb-4">{event.organizer_name || "Official Partner"}</h3>
                                <p className="text-gray-500 max-w-md">Experience events crafted with excellence and brought to you by industry legends.</p>
                            </div>
                            <Link href="/contact" className="px-8 py-4 bg-white text-[11px] font-black uppercase tracking-widest border border-gray-200 rounded-2xl hover:bg-gray-900 hover:text-white transition-all">
                                Contact Organizer
                            </Link>
                        </div>
                    </div>

                    {/* Floating Ticket Selector Column */}
                    <div className="lg:w-[420px] shrink-0">
                        <div className="sticky top-32">
                            <div className="relative group">
                                {/* Subtle Glow Effect */}
                                <div className="absolute -inset-1 bg-linear-to-r from-blue-600 to-purple-600 rounded-[40px] blur opacity-10 group-hover:opacity-20 transition duration-1000"></div>

                                <TicketSelector tickets={tickets} />
                            </div>
                        </div>
                    </div>

                </div>
            </main>

            <Footer />
        </div>
    );
}
