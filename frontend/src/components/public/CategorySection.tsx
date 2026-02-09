import { Music, Mic2, Palette, ChefHat, Rocket, Ticket } from "lucide-react";
import Link from "next/link";

const categories = [
    { name: "Concert", icon: Music, color: "bg-blue-600", count: 12 },
    { name: "Workshop", icon: Rocket, color: "bg-purple-600", count: 8 },
    { name: "Exhibition", icon: Palette, color: "bg-red-500", count: 5 },
    { name: "Food", icon: ChefHat, color: "bg-orange-500", count: 14 },
    { name: "Seminar", icon: Mic2, color: "bg-emerald-600", count: 6 },
    { name: "Others", icon: Ticket, color: "bg-gray-800", count: 4 },
];

export default function CategorySection() {
    return (
        <section className="max-w-7xl mx-auto px-6 lg:px-10 pt-24">
            <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                {categories.map((cat) => (
                    <Link
                        key={cat.name}
                        href={`/events?category=${cat.name}`}
                        className="group relative bg-white border border-gray-100 p-6 md:p-8 rounded-[24px] md:rounded-[32px] flex flex-col items-center hover:shadow-2xl hover:shadow-gray-200 transition-all duration-500 hover:-translate-y-2 overflow-hidden"
                    >
                        <div className={`w-14 h-14 rounded-2xl ${cat.color} flex items-center justify-center text-white mb-6 group-hover:scale-110 transition-transform shadow-lg`}>
                            <cat.icon className="w-6 h-6" />
                        </div>
                        <h3 className="text-sm font-black uppercase tracking-widest text-gray-900 mb-1">{cat.name}</h3>
                        <span className="text-[10px] font-bold text-gray-400 uppercase tracking-widest">{cat.count} Events</span>
                        
                        {/* Decorative Gradient on hover */}
                        <div className="absolute inset-0 bg-linear-to-b from-transparent via-transparent to-gray-50 opacity-0 group-hover:opacity-100 transition-opacity" />
                    </Link>
                ))}
            </div>
        </section>
    );
}
