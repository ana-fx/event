import { Facebook, Instagram, Twitter, MessageCircle, MapPin, Mail, Phone } from "lucide-react";
import Link from "next/link";

export default function Footer() {
    return (
        <footer className="bg-gray-900 text-white pt-20 pb-10 border-t border-white/5">
            <div className="max-w-7xl mx-auto px-6 lg:px-10">
                <div className="flex flex-col lg:flex-row justify-between gap-12 lg:gap-24 mb-16">
                    {/* Brand Section */}
                    <div className="lg:w-5/12 space-y-8">
                        <div className="flex items-center gap-3">
                            <div className="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center text-white">
                                <MapPin className="w-5 h-5 text-blue-500" />
                            </div>
                            <Link href="/" className="text-2xl font-black tracking-tighter uppercase flex items-center gap-1">
                                <span className="text-blue-600">IN</span>GATE
                            </Link>
                        </div>
                        <p className="text-gray-400 leading-relaxed text-sm max-w-sm">
                            The best platform to discover and book your favorite concert, workshop, and exhibition tickets.
                        </p>
                        <div className="flex gap-4">
                            {[Facebook, Twitter, Instagram].map((Icon, i) => (
                                <a key={i} href="#" className="w-10 h-10 rounded-full bg-white/5 hover:bg-blue-600 flex items-center justify-center transition-all hover:-translate-y-1">
                                    <Icon className="w-5 h-5" />
                                </a>
                            ))}
                            <a href="https://wa.me/6287750581589" target="_blank" className="w-10 h-10 rounded-full bg-white/5 hover:bg-green-600 flex items-center justify-center transition-all hover:-translate-y-1">
                                <MessageCircle className="w-5 h-5" />
                            </a>
                        </div>
                    </div>

                    {/* Links Section */}
                    <div className="lg:w-6/12 grid grid-cols-2 gap-8 md:gap-12">
                        <div>
                            <h3 className="font-bold text-white mb-6 uppercase text-[11px] tracking-widest">Company</h3>
                            <ul className="space-y-3 text-sm text-gray-400">
                                <li><a href="/about-us" className="hover:text-blue-500 transition-colors">About Us</a></li>
                                <li><a href="/contact" className="hover:text-blue-500 transition-colors">Contact</a></li>
                                <li><a href="/admin/login" className="hover:text-blue-500 transition-colors">Staff Login</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 className="font-bold text-white mb-6 uppercase text-[11px] tracking-widest">Get in Touch</h3>
                            <ul className="space-y-4 text-sm text-gray-400">
                                <li className="flex items-start gap-3">
                                    <MapPin className="w-5 h-5 text-blue-500 shrink-0 mt-0.5" />
                                    <span className="leading-relaxed">
                                        Jl. Jendral Sudirman No. 1, Jakarta, Indonesia
                                    </span>
                                </li>
                                <li className="flex items-center gap-3">
                                    <Mail className="w-5 h-5 text-blue-500 shrink-0" />
                                    <a href="mailto:hello@ingate.id" className="hover:text-blue-500 transition-colors">hello@ingate.id</a>
                                </li>
                                <li className="flex items-center gap-3">
                                    <Phone className="w-5 h-5 text-blue-500 shrink-0" />
                                    <a href="https://wa.me/6287750581589" className="hover:text-blue-500 transition-colors">0877-5058-1589</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div className="border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p>&copy; {new Date().getFullYear()} Ingate. All rights reserved.</p>
                    <div className="flex gap-6 text-sm">
                        <a href="/privacy" className="text-gray-500 hover:text-white transition-colors">Privacy Policy</a>
                        <a href="/terms" className="text-gray-500 hover:text-white transition-colors">Terms & Conditions</a>
                    </div>
                </div>
            </div>
        </footer>
    );
}

