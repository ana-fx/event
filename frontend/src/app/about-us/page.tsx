import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import Image from "next/image";

export default function AboutPage() {
    return (
        <div className="min-h-screen bg-white flex flex-col">
            <Navbar />
            <main className="flex-1 w-full">
                {/* Hero Section */}
                <div className="relative py-24 bg-gray-900 text-center text-white overflow-hidden">
                    <div className="absolute inset-0 bg-linear-to-b from-gray-800 to-gray-900 opacity-90"></div>
                    <div className="relative max-w-4xl mx-auto px-6">
                        <h1 className="text-4xl md:text-6xl font-black mb-6 tracking-tight">About Ingate</h1>
                        <p className="text-xl text-gray-300 max-w-2xl mx-auto">
                            We are an innovative ticketing platform dedicated to connecting people with the best events in Indonesia.
                        </p>
                    </div>
                </div>

                {/* Content */}
                <div className="max-w-4xl mx-auto px-6 py-16">
                    <div className="prose prose-lg prose-blue max-w-none text-gray-600">
                        <h3>Our Mission</h3>
                        <p>
                            To simplify the event experience for both organizers and attendees through cutting-edge technology and seamless service.
                        </p>

                        <h3>Why Choose Us?</h3>
                        <ul>
                            <li><strong>Easy Booking:</strong> Fast and secure ticket purchasing process.</li>
                            <li><strong>Reliable Support:</strong> Dedicated customer service team ready to help.</li>
                            <li><strong>Trusted Partners:</strong> We work with top event organizers across the country.</li>
                        </ul>
                    </div>
                </div>
            </main>
            <Footer />
        </div>
    );
}
