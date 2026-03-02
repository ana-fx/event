import { Facebook, Instagram, Twitter, MessageCircle, MapPin, Mail, Phone } from "lucide-react";
import Link from "next/link";
import Image from "next/image";

export default function Footer() {
    return (
        <footer className="bg-brand-dark text-white pt-20 pb-10">
            <div className="max-w-7xl mx-auto px-6 lg:px-10">
                <div className="flex flex-col lg:flex-row justify-between gap-12 lg:gap-24 mb-16">
                    {/* Brand Section */}
                    <div className="lg:w-5/12 space-y-8">
                        <Link href="/" className="inline-block">
                            <Image
                                src="/logo-ingate.png"
                                alt="Ingate Logo"
                                width={140}
                                height={40}
                                className="h-8 md:h-10 w-auto object-contain"
                            />
                        </Link>
                        <p className="text-white/50 leading-relaxed text-sm max-w-sm">
                            The best platform to discover and book your favorite concert, workshop, and exhibition tickets.
                        </p>
                        <div className="flex gap-4">
                            {[Facebook, Twitter, Instagram].map((Icon, i) => (
                                <a key={i} href="#" className="w-10 h-10 rounded-full bg-white/5 hover:bg-primary flex items-center justify-center transition-all hover:-translate-y-1">
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
                            <ul className="space-y-3 text-sm text-white/50">
                                <li><a href="/contact" className="hover:text-primary transition-colors">Contact</a></li>
                                <li><a href="/admin/login" className="hover:text-primary transition-colors">Staff Login</a></li>
                            </ul>
                        </div>
                        <div>
                            <h3 className="font-bold text-white mb-6 uppercase text-[11px] tracking-widest">Get in Touch</h3>
                            <ul className="space-y-4 text-sm text-white/50">
                                <li className="flex items-start gap-3">
                                    <MapPin className="w-5 h-5 text-white shrink-0 mt-0.5" />
                                    <span className="leading-relaxed">
                                        Ponorogo, Jawa Timur
                                    </span>
                                </li>
                                <li className="flex items-center gap-3">
                                    <Mail className="w-5 h-5 text-white shrink-0" />
                                    <a href="mailto:hello@ingate.id" className="hover:text-primary transition-colors">hello@ingate.id</a>
                                </li>
                                <li className="flex items-center gap-3">
                                    <Phone className="w-5 h-5 text-white shrink-0" />
                                    <a href="https://wa.me/6287750581589" className="hover:text-primary transition-colors">0877-5058-1589</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div className="border-t border-white/5 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p>&copy; {new Date().getFullYear()} Ingate. All rights reserved.</p>
                    <div className="flex gap-6 text-sm">
                        <a href="/privacy" className="text-white/30 hover:text-white transition-colors">Privacy Policy</a>
                        <a href="/terms" className="text-white/30 hover:text-white transition-colors">Terms & Conditions</a>
                    </div>
                </div>
            </div>
        </footer>
    );
}

