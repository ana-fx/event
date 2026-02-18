"use client";

import { useState, useEffect } from "react";
import Image from "next/image";
import Link from "next/link";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { getImageUrl } from "@/lib/utils";

interface Banner {
    id: number;
    title: string;
    image_path: string;
    link_url: string;
}

export default function Hero() {
    const [banners, setBanners] = useState<Banner[]>([]);
    const [current, setCurrent] = useState(0);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchBanners = async () => {
            try {
                // Client-side: Always use relative path to avoid Mixed Content/CORS
                const apiUrl = "/api";
                const res = await fetch(`${apiUrl}/banners`);
                const data = await res.json();
                setBanners(data || []);
            } catch (error) {
                console.error("Failed to fetch banners", error);
            } finally {
                setLoading(false);
            }
        };

        fetchBanners();
    }, []);

    const nextSlide = () => setCurrent((prev) => (prev === banners.length - 1 ? 0 : prev + 1));
    const prevSlide = () => setCurrent((prev) => (prev === 0 ? banners.length - 1 : prev - 1));

    useEffect(() => {
        if (banners.length > 0) {
            const timer = setInterval(nextSlide, 5000);
            return () => clearInterval(timer);
        }
    }, [banners.length]);

    if (loading) {
        return <div className="h-[600px] w-full bg-gray-900 animate-pulse" />;
    }

    if (banners.length === 0) {
        return null; // Or a fallback banner
    }

    return (
        <section className="w-full pt-28 md:pt-32 pb-8 bg-linear-to-b from-gray-50 to-white">
            <div className="max-w-7xl mx-auto px-4 md:px-6 lg:px-10">
                <div className="relative h-[220px] md:h-[320px] lg:h-[420px] w-full overflow-hidden rounded-[24px] md:rounded-[40px] shadow-xl shadow-gray-200">
                    {banners.map((slide, index) => (
                        <div
                            key={slide.id}
                            className={`absolute inset-0 transition-all duration-1000 ease-in-out ${index === current ? "opacity-100 scale-100" : "opacity-0 scale-105 pointer-events-none"}`}
                        >
                            <Link href={slide.link_url || "/events"} className="block w-full h-full relative group">
                                <Image
                                    src={getImageUrl(slide.image_path)}
                                    alt={slide.title}
                                    fill
                                    className="object-cover"
                                    priority={index === 0}
                                />
                                {/* Subtle Overlay for safety */}
                                <div className="absolute inset-0 bg-black/5 group-hover:bg-black/0 transition-colors duration-500" />

                                {slide.title && (
                                    <div className="absolute inset-0 flex flex-col items-start justify-end p-6 md:p-10 lg:p-12 text-white bg-linear-to-t from-black/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500">
                                        <h2 className="text-xl md:text-3xl font-black uppercase tracking-tighter">
                                            {slide.title}
                                        </h2>
                                    </div>
                                )}
                            </Link>
                        </div>
                    ))}

                    {/* Navigation Buttons - Scaled down */}
                    {banners.length > 1 && (
                        <>
                            <button
                                onClick={prevSlide}
                                className="absolute left-3 md:left-5 top-1/2 -translate-y-1/2 w-8 h-8 md:w-12 md:h-12 bg-white/80 backdrop-blur-md rounded-full flex items-center justify-center hover:bg-white transition-all text-gray-900 shadow-lg z-20 group"
                            >
                                <ChevronLeft className="w-4 h-4 md:w-5 md:h-5" />
                            </button>
                            <button
                                onClick={nextSlide}
                                className="absolute right-3 md:right-5 top-1/2 -translate-y-1/2 w-8 h-8 md:w-12 md:h-12 bg-white/80 backdrop-blur-md rounded-full flex items-center justify-center hover:bg-white transition-all text-gray-900 shadow-lg z-20 group"
                            >
                                <ChevronRight className="w-4 h-4 md:w-5 md:h-5" />
                            </button>
                        </>
                    )}
                </div>

                {/* Progress Indicators - Tighter spacing */}
                {banners.length > 1 && (
                    <div className="flex justify-center items-center gap-2 mt-6">
                        {banners.map((_, index) => (
                            <button
                                key={index}
                                onClick={() => setCurrent(index)}
                                className={`h-1 transition-all duration-500 rounded-full ${index === current ? "bg-red-500 w-10" : "bg-gray-200 w-5 hover:bg-gray-300"}`}
                            />
                        ))}
                    </div>
                )}
            </div>
        </section>
    );
}

