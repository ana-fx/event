"use client";

import { useEffect, useState } from "react";
import axiosInstance from "@/lib/axios";
import { DollarSign, Ticket, Calendar, Users, TrendingUp, CreditCard, Activity } from "lucide-react";

interface DashboardStats {
    total_revenue: number;
    tickets_sold: number;
    active_events: number;
    total_users: number;
}

export default function AdminDashboard() {
    const [stats, setStats] = useState<DashboardStats | null>(null);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        const fetchStats = async () => {
            try {
                const res = await axiosInstance.get("/admin/dashboard");
                setStats(res.data);
            } catch (error) {
                console.error("Failed to fetch dashboard stats", error);
            } finally {
                setLoading(false);
            }
        };

        fetchStats();
    }, []);

    if (loading) {
        return (
            <div className="flex items-center justify-center h-64">
                <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            </div>
        );
    }

    return (
        <div className="w-full max-w-none space-y-8 animate-fade-in">
            <div className="mb-8">
                <h1 className="text-3xl font-bold text-(--foreground)">Dashboard Overview</h1>
                <p className="text-gray-500 mt-1">Overview of your event platform performance.</p>
            </div>

            <div className="w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                {/* Total Revenue */}
                <div className="bg-(--card) p-6 rounded-2xl border border-(--card-border) shadow-sm group hover:scale-[1.02] transition-transform duration-300">
                    <div className="flex justify-between items-start mb-4">
                        <div className="p-3 bg-blue-500/10 rounded-xl text-blue-600 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                            <CreditCard className="w-6 h-6" />
                        </div>
                    </div>
                    <h3 className="text-gray-500 text-sm font-bold mb-1">Total Revenue</h3>
                    <p className="text-2xl font-bold text-(--foreground)">Rp {(stats?.total_revenue ?? 0).toLocaleString()}</p>
                </div>

                {/* Tickets Sold */}
                <div className="bg-(--card) p-6 rounded-2xl border border-(--card-border) shadow-sm group hover:scale-[1.02] transition-transform duration-300">
                    <div className="flex justify-between items-start mb-4">
                        <div className="p-3 bg-indigo-500/10 rounded-xl text-indigo-600 group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                            <Ticket className="w-6 h-6" />
                        </div>
                    </div>
                    <h3 className="text-gray-500 text-sm font-bold mb-1">Tickets Sold</h3>
                    <p className="text-2xl font-bold text-(--foreground)">{(stats?.tickets_sold ?? 0).toLocaleString()}</p>
                </div>

                {/* Active Events */}
                <div className="bg-(--card) p-6 rounded-2xl border border-(--card-border) shadow-sm group hover:scale-[1.02] transition-transform duration-300">
                    <div className="flex justify-between items-start mb-4">
                        <div className="p-3 bg-emerald-500/10 rounded-xl text-emerald-600 group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                            <Calendar className="w-6 h-6" />
                        </div>
                    </div>
                    <h3 className="text-gray-500 text-sm font-bold mb-1">Active Events</h3>
                    <p className="text-2xl font-bold text-(--foreground)">{stats?.active_events ?? 0}</p>
                </div>

                {/* Total Users */}
                <div className="bg-(--card) p-6 rounded-2xl border border-(--card-border) shadow-sm group hover:scale-[1.02] transition-transform duration-300">
                    <div className="flex justify-between items-start mb-4">
                        <div className="p-3 bg-orange-500/10 rounded-xl text-orange-600 group-hover:bg-orange-600 group-hover:text-white transition-colors">
                            <Users className="w-6 h-6" />
                        </div>
                    </div>
                    <h3 className="text-gray-500 text-sm font-bold mb-1">Total Users</h3>
                    <p className="text-2xl font-bold text-(--foreground)">{stats?.total_users ?? 0}</p>
                </div>
            </div>

            {/* Placeholder for Charts or Recent Activity */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <div className="bg-(--card) p-8 rounded-2xl border border-(--card-border) shadow-xl relative overflow-hidden group">
                    <div className="absolute top-0 right-0 w-32 h-32 bg-linear-to-bl from-(--background) to-transparent opacity-10 group-hover:scale-150 transition-transform duration-700"></div>
                    <h3 className="text-xl font-bold mb-6 flex items-center gap-3 text-gray-800 dark:text-gray-100">
                        <Activity className="w-5 h-5 text-blue-600" />
                        Sales Activity
                    </h3>
                    <p className="text-xs">Coming to Phase 5</p>
                </div>
                <div className="bg-(--card) p-8 rounded-2xl border border-(--card-border) shadow-xl relative overflow-hidden group">
                    <div className="absolute top-0 right-0 w-32 h-32 bg-linear-to-bl from-(--background) to-transparent opacity-10 group-hover:scale-150 transition-transform duration-700"></div>
                    <h3 className="text-xl font-bold mb-6 flex items-center gap-3 text-gray-800 dark:text-gray-100">
                        <Users className="w-5 h-5 text-purple-600" />
                        User Growth
                    </h3>
                    <p className="text-xs">Coming to Phase 5</p>
                </div>
            </div>
        </div>
    );
}
