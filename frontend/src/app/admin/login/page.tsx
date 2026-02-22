"use client";

import { useState } from "react";
import Cookies from "js-cookie";
import Image from "next/image";
import { useRouter } from "next/navigation";
import api from "@/lib/axios";
import { toast } from "react-hot-toast";

export default function AdminLogin() {
    const [email, setEmail] = useState("");
    const [password, setPassword] = useState("");
    const [loading, setLoading] = useState(false);
    const router = useRouter();

    const handleLogin = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);

        try {
            const res = await api.post("/login", { email, password });

            // Assuming backend returns { token, user: { role: 'admin', ... } }
            const { token, user } = res.data;

            Cookies.set("token", token, { expires: 1 }); // 1 day
            Cookies.set("user", JSON.stringify(user), { expires: 1 });

            toast.success("Login successful!");

            // Redirect based on role
            if (user.role === 'admin') router.push("/admin");
            else if (user.role === 'organizer') router.push("/organizer");
            else if (user.role === 'scanner') router.push("/scanner");
            else if (user.role === 'reseller') router.push("/reseller");
            else {
                toast.error("Unknown role");
                setLoading(false);
            }
        } catch (error: any) {
            toast.error(error.response?.data?.error || "Login failed");
        } finally {
            setLoading(false);
        }
    };

    return (
        <div className="min-h-screen bg-gray-50 flex items-center justify-center p-4">
            <div className="bg-white p-8 rounded-3xl shadow-xl w-full max-w-md space-y-8 border border-gray-100">
                <div className="text-center">
                    <div className="mb-6 flex justify-center">
                        <Image
                            src="/logo-ingate.png"
                            alt="Ingate Logo"
                            width={160}
                            height={48}
                            className="h-12 w-auto object-contain"
                            priority
                        />
                    </div>
                    <p className="text-gray-500 mt-2 font-medium">Admin Portal — Sign in to manage your events</p>
                </div>

                <form onSubmit={handleLogin} className="space-y-6">
                    <div>
                        <label className="block text-sm font-bold text-gray-700 mb-2">Email</label>
                        <input
                            type="email"
                            value={email}
                            onChange={(e) => setEmail(e.target.value)}
                            className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            placeholder="admin@example.com"
                            required
                        />
                    </div>

                    <div>
                        <label className="block text-sm font-bold text-gray-700 mb-2">Password</label>
                        <input
                            type="password"
                            value={password}
                            onChange={(e) => setPassword(e.target.value)}
                            className="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-all"
                            placeholder="••••••••"
                            required
                        />
                    </div>

                    <button
                        type="submit"
                        disabled={loading}
                        className="w-full py-3.5 bg-primary text-white font-bold rounded-xl hover:bg-primary transition-colors shadow-lg shadow-primary/20 disabled:opacity-70 disabled:cursor-not-allowed"
                    >
                        {loading ? "Signing in..." : "Sign In"}
                    </button>
                </form>
            </div>
        </div>
    );
}
