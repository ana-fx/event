"use client";

import { useState } from "react";
import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import { Mail, Phone, MapPin, Send, Loader2, CheckCircle } from "lucide-react";
import api from "@/lib/axios";
import { toast } from "react-hot-toast";

export default function ContactPage() {
    const [loading, setLoading] = useState(false);
    const [sent, setSent] = useState(false);
    const [formData, setFormData] = useState({
        name: "",
        email: "",
        subject: "",
        message: ""
    });

    const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement>) => {
        setFormData({ ...formData, [e.target.name]: e.target.value });
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);

        try {
            await api.post("/contact", formData);
            setSent(true);
            toast.success("Message sent successfully!");
            setFormData({ name: "", email: "", subject: "", message: "" });
        } catch (error) {
            console.error(error);
            toast.error("Failed to send message. Please try again.");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-white flex flex-col selection:bg-primary/10 selection:text-primary">
            <Navbar />

            <main className="flex-1 flex flex-col lg:flex-row min-h-screen pt-44">
                {/* Left Side: Editorial Info */}
                <div className="lg:w-2/5 p-8 lg:p-20 bg-gray-50 flex flex-col justify-center relative overflow-hidden">
                    {/* Decorative Background Element */}
                    <div className="absolute -top-24 -left-24 w-96 h-96 bg-primary/5 rounded-full blur-3xl animate-pulse"></div>
                    <div className="absolute -bottom-24 -right-24 w-64 h-64 bg-brand-dark/5 rounded-full blur-3xl"></div>

                    <div className="relative z-10 space-y-12">
                        <div>
                            <span className="text-primary text-[10px] font-black uppercase tracking-[0.5em] mb-6 block">Contact Us</span>
                            <h1 className="text-6xl lg:text-8xl font-black text-brand-dark tracking-tighter uppercase leading-[0.85] font-heading">
                                Let&apos;s <br />
                                <span className="text-gray-300">Connect</span>
                                <span className="text-primary">.</span>
                            </h1>
                        </div>

                        <div className="space-y-8 max-w-sm">
                            <p className="text-lg text-gray-500 font-medium leading-relaxed font-body">
                                Have a vision for an event? Or just want to say hi? We&apos;re always open to new connections and collaborations.
                            </p>

                            <div className="space-y-6 pt-4 border-t border-gray-200">
                                <a href="mailto:support@ingate.id" className="flex items-center gap-4 group transition-all">
                                    <div className="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center group-hover:bg-brand-dark group-hover:border-brand-dark transition-all">
                                        <Mail className="w-5 h-5 text-gray-400 group-hover:text-white transition-colors" />
                                    </div>
                                    <div>
                                        <p className="text-[10px] uppercase font-black tracking-widest text-gray-400">Email</p>
                                        <p className="font-bold text-brand-dark">support@ingate.id</p>
                                    </div>
                                </a>

                                <div className="flex items-center gap-4 group cursor-default">
                                    <div className="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center">
                                        <MapPin className="w-5 h-5 text-gray-400" />
                                    </div>
                                    <div>
                                        <p className="text-[10px] uppercase font-black tracking-widest text-gray-400">Location</p>
                                        <p className="font-bold text-brand-dark">Ponorogo, Jawa Timur</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Right Side: Interactive Form */}
                <div className="lg:w-3/5 p-8 lg:p-24 flex flex-col justify-center bg-white">
                    <div className="max-w-xl w-full mx-auto">
                        {sent ? (
                            <div className="text-center py-20 animate-in fade-in zoom-in duration-500">
                                <div className="w-24 h-24 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-8">
                                    <CheckCircle className="w-12 h-12" />
                                </div>
                                <h2 className="text-4xl font-black text-brand-dark tracking-tighter uppercase mb-4">Success!</h2>
                                <p className="text-gray-500 font-medium mb-10">Your message has been received. We&apos;ll get back to you shortly.</p>
                                <button
                                    onClick={() => setSent(false)}
                                    className="px-10 py-4 bg-brand-dark text-white text-[10px] font-black uppercase tracking-[0.3em] rounded-full hover:bg-primary transition-all duration-300 shadow-xl shadow-brand-dark/10"
                                >
                                    Send Another
                                </button>
                            </div>
                        ) : (
                            <>
                                <div className="mb-12">
                                    <h2 className="text-2xl font-black text-brand-dark uppercase tracking-tight font-heading mb-2">Send an inquiry</h2>
                                    <div className="w-12 h-1 bg-primary rounded-full"></div>
                                </div>

                                <form onSubmit={handleSubmit} className="space-y-8">
                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                                        <div className="group relative">
                                            <label className="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 transition-colors group-focus-within:text-primary">Name</label>
                                            <input
                                                type="text"
                                                name="name"
                                                required
                                                value={formData.name}
                                                onChange={handleChange}
                                                className="w-full bg-transparent border-b-2 border-gray-100 py-3 outline-none focus:border-primary transition-all font-medium text-brand-dark placeholder:text-gray-200"
                                                placeholder="Enter your name"
                                            />
                                        </div>
                                        <div className="group relative">
                                            <label className="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 transition-colors group-focus-within:text-primary">Email</label>
                                            <input
                                                type="email"
                                                name="email"
                                                required
                                                value={formData.email}
                                                onChange={handleChange}
                                                className="w-full bg-transparent border-b-2 border-gray-100 py-3 outline-none focus:border-primary transition-all font-medium text-brand-dark placeholder:text-gray-200"
                                                placeholder="your@email.com"
                                            />
                                        </div>
                                    </div>

                                    <div className="group relative">
                                        <label className="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 transition-colors group-focus-within:text-primary">Subject</label>
                                        <input
                                            type="text"
                                            name="subject"
                                            required
                                            value={formData.subject}
                                            onChange={handleChange}
                                            className="w-full bg-transparent border-b-2 border-gray-100 py-3 outline-none focus:border-primary transition-all font-medium text-brand-dark placeholder:text-gray-200"
                                            placeholder="What is this about?"
                                        />
                                    </div>

                                    <div className="group relative">
                                        <label className="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 transition-colors group-focus-within:text-primary">Message</label>
                                        <textarea
                                            name="message"
                                            required
                                            rows={4}
                                            value={formData.message}
                                            onChange={handleChange}
                                            className="w-full bg-transparent border-b-2 border-gray-100 py-3 outline-none focus:border-primary transition-all font-medium text-brand-dark placeholder:text-gray-200 resize-none"
                                            placeholder="Write your thoughts here..."
                                        ></textarea>
                                    </div>

                                    <div className="pt-6">
                                        <button
                                            type="submit"
                                            disabled={loading}
                                            className="group flex items-center gap-6 text-[11px] font-black uppercase tracking-[0.4em] text-brand-dark disabled:opacity-50 transition-all hover:text-primary"
                                        >
                                            {loading ? 'Sending...' : 'Send Message'}
                                            <div className="w-16 h-16 rounded-full border-2 border-gray-100 flex items-center justify-center group-hover:border-primary group-hover:bg-primary group-hover:text-white transition-all transform group-hover:rotate-45">
                                                {loading ? <Loader2 className="w-6 h-6 animate-spin" /> : <Send className="w-6 h-6 translate-x-0.5 -translate-y-0.5" />}
                                            </div>
                                        </button>
                                    </div>
                                </form>
                            </>
                        )}
                    </div>
                </div>
            </main>

            <Footer />
        </div>
    );
}
