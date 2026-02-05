"use client";

import { useState, useEffect } from "react";
import Image from "next/image";
import Link from "next/link";
import { ChevronLeft, ChevronRight } from "lucide-react";

const slides = [
    {
        id: 1,
        image: "https://images.unsplash.com/photo-1540039155733-5bb30b53aa14?q=80&w=1974&auto=format&fit=crop",
        title: "Experience the Music Live",
        subtitle: "Book tickets for the biggest concerts in 2024.",
        cta: "Explore Concerts",
        link: "/events?category=concert"
    },
    {
        id: 2,
        image: "https://images.unsplash.com/photo-1511578314322-379afb476865?q=80&w=2069&auto=format&fit=crop",
        title: "Learn from the Best",
        subtitle: "Join exclusive workshops and masterclasses.",
        cta: "Browse Workshops",
        link: "/events?category=workshop"
    },
    {
        id: 3,
        image: "https://images.unsplash.com/photo-1492684223066-81342ee5ff30?q=80&w=2070&auto=format&fit=crop",
        title: "Unforgettable Nightlife",
        subtitle: "Discover the wildest parties in your city.",
        cta: "Find Parties",
        link: "/events?category=party"
    }
];

export default function Hero() {
    const [current, setCurrent] = useState(0);

    const nextSlide = () => setCurrent((prev) => (prev === slides.length - 1 ? 0 : prev + 1));
    const prevSlide = () => setCurrent((prev) => (prev === 0 ? slides.length - 1 : prev - 1));

    useEffect(() => {
        const timer = setInterval(nextSlide, 5000);
        return () => clearInterval(timer);
    }, []);

    return (
        <div className="relative h-[600px] w-full overflow-hidden bg-gray-900">
            {slides.map((slide, index) => (
                <div
                    key={slide.id}
                    className={`absolute inset-0 transition-opacity duration-1000 ease-in-out ${index === current ? "opacity-100" : "opacity-0"}`}
                >
                    <div className="relative h-full w-full">
                        <Image
                            src={slide.image}
                            alt={slide.title}
                            fill
                            className="object-cover"
                            priority={index === 0}
                        />
                        <div className="absolute inset-0 bg-black/50" />
                        <div className="absolute inset-0 flex flex-col items-center justify-center text-center text-white p-4">
                            <h1 className="text-4xl md:text-6xl font-extrabold mb-4 tracking-tight animate-fade-in">
                                {slide.title}
                            </h1>
                            <p className="text-lg md:text-xl text-gray-200 mb-8 max-w-2xl animate-fade-in delay-100">
                                {slide.subtitle}
                            </p>
                            <Link
                                href={slide.link}
                                className="px-8 py-3 bg-blue-600 rounded-full font-bold hover:bg-blue-700 transition-transform active:scale-95 shadow-lg shadow-blue-500/30 animate-fade-in delay-200"
                            >
                                {slide.cta}
                            </Link>
                        </div>
                    </div>
                </div>
            ))}

            {/* Navigation Buttons */}
            <button
                onClick={prevSlide}
                className="absolute left-4 top-1/2 -translate-y-1/2 p-2 bg-white/10 backdrop-blur-sm rounded-full hover:bg-white/20 transition-colors text-white hidden md:block"
            >
                <ChevronLeft className="w-8 h-8" />
            </button>
            <button
                onClick={nextSlide}
                className="absolute right-4 top-1/2 -translate-y-1/2 p-2 bg-white/10 backdrop-blur-sm rounded-full hover:bg-white/20 transition-colors text-white hidden md:block"
            >
                <ChevronRight className="w-8 h-8" />
            </button>

            {/* Dots */}
            <div className="absolute bottom-8 left-1/2 -translate-x-1/2 flex gap-2">
                {slides.map((_, index) => (
                    <button
                        key={index}
                        onClick={() => setCurrent(index)}
                        className={`w-3 h-3 rounded-full transition-all ${index === current ? "bg-white w-8" : "bg-white/50 hover:bg-white/80"}`}
                    />
                ))}
            </div>
        </div>
    );
}
