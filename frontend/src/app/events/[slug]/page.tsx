import { notFound } from "next/navigation";
import Image from "next/image";
import { Calendar, MapPin, Clock, Share2, Info, ChevronLeft, Globe } from "lucide-react";
// Globe still used in fallback organizer block
import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import TicketSelector from "@/components/public/TicketSelector";
import { Metadata } from "next";
import { getImageUrl } from "@/lib/utils";
import Link from "next/link";
import MobileBookingBar from "@/components/public/MobileBookingBar";
import OrganizerModal from "@/components/public/OrganizerModal";

// Define Types (match backend response)
interface Event {
    id: number;
    name: string;
    description: string;
    youtube_link?: string;
    start_date: string;
    end_date: string;
    location: string;
    city: string;
    banner_path: string | null;
    thumbnail_path: string | null;
    seo_title: string;
    seo_description: string;
    organizer_id: number | null;
    organizer_name: string;
    organizer_logo_path: string | null;
    google_map_embed: string | null;
    terms: string | null;
    province: string;
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
        const backendUrl = process.env.BACKEND_URL;
        const publicApiUrl = process.env.NEXT_PUBLIC_API_URL;

        if (!backendUrl && !publicApiUrl) {
            console.error('[Server] ERROR: Neither BACKEND_URL nor NEXT_PUBLIC_API_URL is set!');
            return null;
        }

        let apiUrl = "";
        if (backendUrl) {
            const cleanBase = backendUrl.endsWith('/') ? backendUrl.slice(0, -1) : backendUrl;
            apiUrl = `${cleanBase}/api`;
        } else {
            apiUrl = publicApiUrl!;
        }

        console.log(`[Server] Fetching event detail from: ${apiUrl}/events/detail?slug=${slug}`);

        const res = await fetch(`${apiUrl}/events/detail?slug=${slug}`, {
            cache: 'no-store'
        });

        if (!res.ok) {
            console.error(`[Server] Failed to fetch event detail. Status: ${res.status}`);
            return null;
        }
        return res.json();
    } catch (error) {
        console.error("[Server] Error in getEvent:", error);
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
    const minPrice = (tickets || []).length > 0 ? Math.min(...tickets.map(t => t.price)) : 0;

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
        <div className="min-h-screen bg-white flex flex-col">
            <Navbar />

            {/* Back Button */}
            <div className="fixed top-24 left-6 md:left-10 z-40">
                <Link
                    href="/events"
                    className="flex items-center gap-2 px-4 py-2 bg-white/80 backdrop-blur-md border border-gray-100 rounded-full text-xs font-black uppercase tracking-widest text-brand-dark hover:bg-brand-dark hover:text-white transition-all shadow-sm"
                >
                    <ChevronLeft className="w-4 h-4" />
                    Back
                </Link>
            </div>

            {/* Neo-Editorial Header Section */}
            <div className="pt-[80px] bg-stone-50 border-b border-stone-200/60 overflow-visible relative z-10">
                <div className="max-w-7xl mx-auto px-6 lg:px-10 pt-16 pb-24 md:pt-24 md:pb-32">
                    <div className="flex flex-col lg:flex-row gap-16 lg:items-center">

                        <div className="flex-1 space-y-12 text-center lg:text-left">
                            <div className="space-y-4">
                                <span className="text-primary text-[10px] font-black uppercase tracking-[0.5em] block">Exclusive Event</span>
                                <h1 className="text-4xl sm:text-6xl md:text-7xl lg:text-8xl font-black text-brand-dark leading-[0.95] tracking-tighter uppercase font-heading">
                                    {event.name}
                                </h1>
                            </div>

                            {/* Ticket Strip Meta-info */}
                            <div className="inline-grid grid-cols-2 sm:grid-cols-3 gap-px bg-stone-200/50 p-px rounded-[24px] overflow-hidden border border-stone-200/60 shadow-sm max-w-fit mx-auto lg:mx-0 backdrop-blur-sm">
                                <div className="col-span-2 sm:col-span-1 px-8 py-5 bg-white flex flex-col gap-1 items-center sm:items-start min-w-[180px]">
                                    <span className="text-[10px] font-black uppercase tracking-[0.2em] text-stone-400">Date</span>
                                    <span className="font-bold text-sm text-brand-dark whitespace-nowrap">{formatDate(event.start_date)}</span>
                                </div>
                                <div className="px-8 py-5 bg-white flex flex-col gap-1 items-center sm:items-start min-w-[140px]">
                                    <span className="text-[10px] font-black uppercase tracking-[0.2em] text-stone-400">Time</span>
                                    <span className="font-bold text-sm text-brand-dark">{formatTime(event.start_date)} WIB</span>
                                </div>
                                <div className="px-8 py-5 bg-white flex flex-col gap-1 items-center sm:items-start min-w-[140px]">
                                    <span className="text-[10px] font-black uppercase tracking-[0.2em] text-stone-400">City</span>
                                    <span className="font-bold text-sm text-brand-dark">{event.city || "Jakarta"}</span>
                                </div>
                            </div>

                            <div className="flex items-center justify-center lg:justify-start gap-3 opacity-60">
                                <MapPin className="w-4 h-4 text-stone-400" />
                                <span className="text-xs font-bold uppercase tracking-widest text-stone-500">{event.location}</span>
                            </div>
                        </div>

                        {/* Floating Poster Section - Aligned with text */}
                        {event.thumbnail_path && (
                            <div className="relative group/poster mx-auto lg:mx-0">
                                <div className="absolute -inset-4 bg-stone-900/5 blur-3xl rounded-full opacity-60"></div>
                                <div className="w-64 sm:w-80 lg:w-[450px] xl:w-[500px] aspect-square relative rounded-[48px] overflow-hidden shadow-[0_48px_96px_-24px_rgba(0,0,0,0.35)] border-12 border-white z-10 transition-all duration-700 group-hover/poster:scale-[1.02] group-hover/poster:shadow-[0_64px_128px_-32px_rgba(0,0,0,0.45)]">
                                    <Image
                                        src={getImageUrl(event.thumbnail_path)}
                                        alt={event.name}
                                        fill
                                        className="object-cover"
                                        priority
                                    />
                                    {/* Glass reflection effect */}
                                    <div className="absolute inset-0 bg-linear-to-tr from-white/10 via-transparent to-transparent pointer-events-none" />
                                </div>
                            </div>
                        )}
                    </div>
                </div>
            </div>

            <main className="max-w-7xl mx-auto px-6 lg:px-10 pt-24 pb-20 md:pt-40 md:pb-32 w-full flex-1 relative z-0">

                <div className="flex flex-col lg:flex-row gap-12 lg:gap-16 xl:gap-24">

                    {/* Editorial Content Column */}
                    <div className="flex-1 min-w-0 space-y-20">
                        {/* Event Intro Panel */}
                        <div className="space-y-12">
                            <div className="space-y-8">
                                <span className="text-primary text-[10px] font-black uppercase tracking-[0.4em] block">About Event</span>
                                <h2 className="text-4xl md:text-5xl font-black text-brand-dark tracking-tighter uppercase leading-none font-heading">
                                    Story of <br /> <span className="text-gray-300">the Night</span>
                                </h2>
                            </div>
                            <div className="prose prose-xl prose-gray max-w-none text-gray-600 font-medium leading-[1.8] font-body first-letter:text-7xl first-letter:font-black first-letter:text-primary first-letter:mr-4 first-letter:float-left [&_img]:max-w-full [&_img]:h-auto [&_iframe]:max-w-full [&_iframe]:h-auto"
                                dangerouslySetInnerHTML={{ __html: event.description }}
                            />
                        </div>

                        {/* YouTube Experience Section */}
                        {event.youtube_link && (
                            <div className="space-y-12 animate-fade-in-up">
                                <div className="space-y-4">
                                    <span className="text-primary text-[10px] font-black uppercase tracking-[0.4em] block">Visual Experience</span>
                                    <h2 className="text-4xl md:text-5xl font-black text-brand-dark tracking-tighter uppercase leading-none font-heading">
                                        Event <br /> <span className="text-gray-300">Teaser</span>
                                    </h2>
                                </div>
                                <div className="relative aspect-video rounded-[32px] md:rounded-[48px] overflow-hidden shadow-2xl border-4 md:border-8 border-white bg-stone-100 transform-gpu transition-all duration-700 hover:scale-[1.01]">
                                    <iframe
                                        src={`https://www.youtube.com/embed/${(() => {
                                            const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/;
                                            const match = event.youtube_link.match(regExp);
                                            return (match && match[2].length === 11) ? match[2] : null;
                                        })()}?autoplay=0&rel=0`}
                                        title="Event Trailer"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowFullScreen
                                        className="absolute inset-0 w-full h-full"
                                    />
                                </div>
                            </div>
                        )}
                        {/* Organizer Info Section */}
                        {event.organizer_id ? (
                            <OrganizerModal
                                organizerId={event.organizer_id}
                                organizerName={event.organizer_name || "Official Partner"}
                                organizerLogo={event.organizer_logo_path}
                            />
                        ) : (
                            <div className="p-10 rounded-[40px] bg-gray-50 border border-gray-100 flex flex-col md:flex-row gap-8 items-center">
                                <div className="w-24 h-24 rounded-[28px] bg-white p-2 shadow-xl relative overflow-hidden flex items-center justify-center">
                                    <Globe className="w-10 h-10 text-gray-300" />
                                </div>
                                <div className="flex-1 text-center md:text-left">
                                    <span className="text-[10px] font-black uppercase tracking-[0.3em] text-primary mb-2 block">Our Trusted Partner</span>
                                    <h3 className="text-3xl font-black text-brand-dark tracking-tight uppercase mb-2">{event.organizer_name || "Official Partner"}</h3>
                                    <p className="text-gray-500 max-w-md text-sm">Experience events crafted with excellence and brought to you by industry legends.</p>
                                </div>
                            </div>
                        )}


                        {/* Map & Venue Section */}
                        {event.google_map_embed && (
                            <div className="space-y-8 animate-fade-in-up">
                                <div className="flex items-center gap-4 mb-4">
                                    <div className="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center">
                                        <MapPin className="w-5 h-5 text-red-600" />
                                    </div>
                                    <div>
                                        <h2 className="text-2xl font-black text-brand-dark uppercase tracking-tighter font-heading">Venue & Location</h2>
                                        <p className="text-gray-400 text-[10px] font-black uppercase tracking-[0.2em]">{event.city}</p>
                                    </div>
                                </div>
                                <div
                                    className="w-full h-[450px] rounded-[40px] overflow-hidden shadow-2xl border-8 border-gray-50 grayscale hover:grayscale-0 transition-all duration-700"
                                    dangerouslySetInnerHTML={{ __html: event.google_map_embed }}
                                />
                            </div>
                        )}

                        {/* T&C Section */}
                        {event.terms && (
                            <div className="p-8 md:p-12 rounded-[32px] md:rounded-[40px] bg-linear-to-br from-brand-dark to-black text-white relative overflow-hidden shadow-2xl animate-fade-in-up">
                                <div className="absolute top-0 right-0 p-8 md:p-12 opacity-10">
                                    <Info className="w-24 h-24 md:w-32 md:h-32" />
                                </div>
                                <div className="relative z-10">
                                    <div className="flex items-center gap-4 mb-8">
                                        <div className="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center backdrop-blur-md">
                                            <Info className="w-5 h-5 text-white" />
                                        </div>
                                        <h2 className="text-xl md:text-2xl font-black uppercase tracking-widest italic font-heading">Terms & Conditions</h2>
                                    </div>
                                    <div
                                        className="prose prose-invert prose-sm max-w-none text-gray-400 leading-relaxed font-body mt-4
                                                   [&_ul]:list-disc [&_ul]:pl-5 [&_ol]:list-decimal [&_ol]:pl-5 [&_li]:mb-2 [&_p]:mb-4"
                                        dangerouslySetInnerHTML={{ __html: event.terms }}
                                    />
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Floating Ticket Selector Column */}
                    <div id="booking-section" className="lg:w-[360px] xl:w-[420px] shrink-0">
                        <div className="sticky top-32">
                            <div className="relative group">
                                {/* Subtle Glow Effect */}
                                <div className="absolute -inset-1 bg-linear-to-r from-primary to-purple-600 rounded-[40px] blur opacity-10 group-hover:opacity-20 transition duration-1000"></div>

                                <TicketSelector tickets={tickets} eventSlug={slug} />
                            </div>
                        </div>
                    </div>

                </div>
            </main>

            <Footer />

            {/* Mobile Sticky Booking Bar */}
            <MobileBookingBar minPrice={minPrice} />
        </div>
    );
}
