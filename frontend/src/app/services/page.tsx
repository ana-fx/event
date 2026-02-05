import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import { Ticket, Mic2, Star, ShieldCheck, Users, BarChart } from "lucide-react";

export default function ServicesPage() {
    const services = [
        {
            icon: <Ticket className="w-10 h-10 text-blue-600" />,
            title: "Ticketing System",
            description: "Advanced ticketing platform with QR code scanning, multi-tier pricing, and real-time inventory management."
        },
        {
            icon: <Mic2 className="w-10 h-10 text-purple-600" />,
            title: "Event Promotion",
            description: "Boost your event visibility through our featured listings, email marketing, and social media integration."
        },
        {
            icon: <ShieldCheck className="w-10 h-10 text-green-600" />,
            title: "Secure Access Control",
            description: "Robust entry management tools for gatekeepers to verify tickets instantly and prevent fraud."
        },
        {
            icon: <BarChart className="w-10 h-10 text-orange-600" />,
            title: "Analytics & Reporting",
            description: "Comprehensive dashboard providing insights on sales, attendee demographics, and revenue."
        },
        {
            icon: <Users className="w-10 h-10 text-indigo-600" />,
            title: "Reseller Network",
            description: "Expand your reach by leveraging our network of trusted resellers to sell tickets on your behalf."
        },
        {
            icon: <Star className="w-10 h-10 text-yellow-500" />,
            title: "VIP Experiences",
            description: "Curated premium packages for exclusive events, offering potential revenue uplifts."
        }
    ];

    return (
        <div className="min-h-screen bg-gray-50 flex flex-col">
            <Navbar />

            <main className="flex-1 w-full">
                {/* Hero */}
                <div className="bg-gray-900 py-24 px-6 text-center text-white relative overflow-hidden mt-[80px]">
                    <div className="absolute inset-0 bg-linear-to-r from-blue-900 to-gray-900 opacity-90"></div>
                    <div className="relative max-w-3xl mx-auto">
                        <h1 className="text-4xl md:text-6xl font-black mb-6 tracking-tight">Our Services</h1>
                        <p className="text-xl text-gray-300">
                            Comprehensive solutions for event organizers, from small gigs to large-scale festivals.
                        </p>
                    </div>
                </div>

                {/* Services Grid */}
                <div className="max-w-7xl mx-auto px-6 py-20">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                        {services.map((service, index) => (
                            <div key={index} className="bg-white p-8 rounded-2xl shadow-sm hover:shadow-xl transition-shadow border border-gray-100 group">
                                <div className="mb-6 p-4 bg-gray-50 rounded-xl w-fit group-hover:scale-110 transition-transform duration-300">
                                    {service.icon}
                                </div>
                                <h3 className="text-xl font-bold text-gray-900 mb-3">{service.title}</h3>
                                <p className="text-gray-500 leading-relaxed">{service.description}</p>
                            </div>
                        ))}
                    </div>
                </div>

                {/* CTA */}
                <div className="bg-blue-600 py-16 px-6 text-center text-white">
                    <h2 className="text-3xl font-bold mb-4">Ready to Organize Your Event?</h2>
                    <p className="max-w-xl mx-auto mb-8 opacity-90">Join thousands of successful organizers who trust Ingate for their ticketing needs.</p>
                    <a href="/contact" className="inline-block px-8 py-3 bg-white text-blue-600 font-bold rounded-full hover:bg-gray-100 transition-colors shadow-lg">
                        Get Started Today
                    </a>
                </div>
            </main>

            <Footer />
        </div>
    );
}
