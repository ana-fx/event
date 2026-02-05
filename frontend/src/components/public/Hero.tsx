"use client";

import { useState, useEffect } from "react";
import Image from "next/image";
import Link from "next/link";
import { ChevronLeft, ChevronRight } from "lucide-react";

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
                const res = await fetch("http://localhost:8080/api/banners");
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
        <div className="relative h-[400px] md:h-[600px] w-full overflow-hidden bg-gray-900">
            {banners.map((slide, index) => (
                <div
                    key={slide.id}
                    className={`absolute inset-0 transition-opacity duration-1000 ease-in-out ${index === current ? "opacity-100" : "opacity-0"}`}
                >
                    <div className="relative h-full w-full">
                        <Link href={slide.link_url || "/events"} className="block w-full h-full relative">
                            <Image
                                src={`http://localhost:8080${slide.image_path}`}
                                alt={slide.title}
                                fill
                                className="object-cover"
                                priority={index === 0}
                            />
                            <div className="absolute inset-0 bg-black/20" />
                            {slide.title && (
                                <div className="absolute inset-0 flex flex-col items-center justify-center text-center text-white p-4">
                                    <h1 className="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight drop-shadow-lg">
                                        {slide.title}
                                    </h1>
                                </div>
                            )}
                        </Link>
                    </div>
                </div>
            ))}

            {/* Navigation Buttons */}
            {banners.length > 1 && (
                <>
                    <button
                        onClick={prevSlide}
                        className="absolute left-4 top-1/2 -translate-y-1/2 p-2 bg-white/10 backdrop-blur-sm rounded-full hover:bg-white/20 transition-colors text-white hidden md:block z-10"
                    >
                        <ChevronLeft className="w-8 h-8" />
                    </button>
                    <button
                        onClick={nextSlide}
                        className="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-white/10 backdrop-blur-sm rounded-full hover:bg-white/20 transition-colors text-white hidden md:block z-10"
                    >
                        <ChevronRight className="w-8 h-8" />
                    </button>

                    {/* Dots */}
                    <div className="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2 z-10">
                        {banners.map((_, index) => (
                            <button
                                key={index}
                                onClick={() => setCurrent(index)}
                                className={`w-3 h-3 rounded-full transition-all ${index === current ? "bg-white w-8" : "bg-white/50 hover:bg-white/80"}`}
                            />
                        ))}
                    </div>
                </>
            )}
        </div>
    );
}

