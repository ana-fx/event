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
        <div className="min-h-screen bg-gray-50 flex flex-col">
            <Navbar />

            {/* Hero */}
            <div className="relative bg-gray-900 py-24 px-6 text-center text-white mt-[80px]">
                <div className="absolute inset-0 bg-linear-to-b from-gray-800 to-gray-900 opacity-90"></div>
                <div className="relative max-w-2xl mx-auto">
                    <h1 className="text-4xl md:text-5xl font-black mb-4">Get in Touch</h1>
                    <p className="text-xl text-gray-300">Have questions or need support? We're here to help.</p>
                </div>
            </div>

            <main className="max-w-6xl mx-auto w-full px-6 py-16 -mt-16 relative z-10 grid grid-cols-1 lg:grid-cols-3 gap-8">
                {/* Contact Info */}
                <div className="bg-white p-8 rounded-2xl shadow-xl border border-gray-100 flex flex-col justify-between h-full">
                    <div>
                        <h2 className="text-2xl font-bold text-gray-900 mb-6">Contact Information</h2>
                        <div className="space-y-6">
                            <div className="flex items-start gap-4">
                                <div className="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 shrink-0">
                                    <Mail className="w-6 h-6" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-gray-900">Email</h3>
                                    <p className="text-gray-500">support@ingate.id</p>
                                    <p className="text-gray-500">partners@ingate.id</p>
                                </div>
                            </div>
                            <div className="flex items-start gap-4">
                                <div className="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 shrink-0">
                                    <Phone className="w-6 h-6" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-gray-900">Phone</h3>
                                    <p className="text-gray-500">+62 21 1234 5678</p>
                                    <p className="text-xs text-gray-400">Mon-Fri, 9am - 5pm WIB</p>
                                </div>
                            </div>
                            <div className="flex items-start gap-4">
                                <div className="w-12 h-12 bg-blue-50 rounded-lg flex items-center justify-center text-blue-600 shrink-0">
                                    <MapPin className="w-6 h-6" />
                                </div>
                                <div>
                                    <h3 className="font-bold text-gray-900">Office</h3>
                                    <p className="text-gray-500">
                                        Jl. Jend. Sudirman No. 1<br />
                                        Jakarta Selatan, Indonesia
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Contact Form */}
                <div className="bg-white p-8 rounded-2xl shadow-xl border border-gray-100 lg:col-span-2">
                    {sent ? (
                        <div className="h-full flex flex-col items-center justify-center text-center py-12">
                            <div className="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-6 animate-in zoom-in">
                                <CheckCircle className="w-10 h-10" />
                            </div>
                            <h2 className="text-2xl font-bold text-gray-900 mb-2">Message Sent!</h2>
                            <p className="text-gray-500 max-w-md mx-auto mb-8">
                                Thank you for reaching out. Our team will get back to you as soon as possible via email.
                            </p>
                            <button
                                onClick={() => setSent(false)}
                                className="px-6 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors"
                            >
                                Send Another Message
                            </button>
                        </div>
                    ) : (
                        <>
                            <h2 className="text-2xl font-bold text-gray-900 mb-6">Send us a Message</h2>
                            <form onSubmit={handleSubmit} className="space-y-6">
                                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-2">Your Name</label>
                                        <input
                                            type="text"
                                            name="name"
                                            required
                                            value={formData.name}
                                            onChange={handleChange}
                                            className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all"
                                            placeholder="John Doe"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-sm font-bold text-gray-700 mb-2">Email Address</label>
                                        <input
                                            type="email"
                                            name="email"
                                            required
                                            value={formData.email}
                                            onChange={handleChange}
                                            className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all"
                                            placeholder="john@example.com"
                                        />
                                    </div>
                                </div>
                                <div>
                                    <label className="block text-sm font-bold text-gray-700 mb-2">Subject</label>
                                    <input
                                        type="text"
                                        name="subject"
                                        required
                                        value={formData.subject}
                                        onChange={handleChange}
                                        className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all"
                                        placeholder="How can we help?"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-bold text-gray-700 mb-2">Message</label>
                                    <textarea
                                        name="message"
                                        required
                                        rows={5}
                                        value={formData.message}
                                        onChange={handleChange}
                                        className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 outline-none transition-all resize-none"
                                        placeholder="Tell us more about your inquiry..."
                                    ></textarea>
                                </div>
                                <div className="flex justify-end">
                                    <button
                                        type="submit"
                                        disabled={loading}
                                        className="px-8 py-3 bg-blue-600 text-white font-bold rounded-xl shadow-lg shadow-blue-600/20 hover:bg-blue-700 hover:-translate-y-1 transition-all disabled:opacity-70 disabled:hover:translate-y-0 flex items-center gap-2"
                                    >
                                        {loading ? <Loader2 className="w-5 h-5 animate-spin" /> : <Send className="w-5 h-5" />}
                                        Send Message
                                    </button>
                                </div>
                            </form>
                        </>
                    )}
                </div>
            </main>

            <Footer />
        </div>
    );
}
