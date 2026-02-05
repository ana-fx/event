"use client";

import { useEffect, useState } from "react";
import axiosInstance from "@/lib/axios";
import { DollarSign, Ticket, Calendar, Users, TrendingUp } from "lucide-react";

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
        <div className="space-y-8 animate-fade-in">
            <div>
                <h2 className="text-3xl font-bold text-gray-900 tracking-tight">Dashboard</h2>
                <p className="text-gray-500 mt-1">Overview of your event platform performance.</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {/* Total Revenue */}
                <div className="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/50 flex flex-col justify-between group hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                    <div className="absolute top-0 right-0 w-32 h-32 bg-green-50 rounded-bl-full -mr-8 -mt-8 opacity-50 group-hover:scale-110 transition-transform" />
                    <div>
                        <div className="w-12 h-12 rounded-2xl bg-green-100 flex items-center justify-center text-green-600 mb-4 shadow-sm">
                            <DollarSign className="w-6 h-6" />
                        </div>
                        <p className="text-sm font-bold text-gray-400 uppercase tracking-wider">Total Revenue</p>
                        <p className="text-3xl font-bold text-gray-900 mt-1">
                            {new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR", maximumFractionDigits: 0 }).format(stats?.total_revenue || 0)}
                        </p>
                    </div>
                </div>

                {/* Tickets Sold */}
                <div className="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/50 flex flex-col justify-between group hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                    <div className="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-bl-full -mr-8 -mt-8 opacity-50 group-hover:scale-110 transition-transform" />
                    <div>
                        <div className="w-12 h-12 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 mb-4 shadow-sm">
                            <Ticket className="w-6 h-6" />
                        </div>
                        <p className="text-sm font-bold text-gray-400 uppercase tracking-wider">Tickets Sold</p>
                        <p className="text-3xl font-bold text-gray-900 mt-1">{stats?.tickets_sold || 0}</p>
                    </div>
                </div>

                {/* Active Events */}
                <div className="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/50 flex flex-col justify-between group hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                    <div className="absolute top-0 right-0 w-32 h-32 bg-purple-50 rounded-bl-full -mr-8 -mt-8 opacity-50 group-hover:scale-110 transition-transform" />
                    <div>
                        <div className="w-12 h-12 rounded-2xl bg-purple-100 flex items-center justify-center text-purple-600 mb-4 shadow-sm">
                            <Calendar className="w-6 h-6" />
                        </div>
                        <p className="text-sm font-bold text-gray-400 uppercase tracking-wider">Active Events</p>
                        <p className="text-3xl font-bold text-gray-900 mt-1">{stats?.active_events || 0}</p>
                    </div>
                </div>

                {/* Total Users */}
                <div className="bg-white p-6 rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/50 flex flex-col justify-between group hover:-translate-y-1 transition-all duration-300 relative overflow-hidden">
                    <div className="absolute top-0 right-0 w-32 h-32 bg-orange-50 rounded-bl-full -mr-8 -mt-8 opacity-50 group-hover:scale-110 transition-transform" />
                    <div>
                        <div className="w-12 h-12 rounded-2xl bg-orange-100 flex items-center justify-center text-orange-600 mb-4 shadow-sm">
                            <Users className="w-6 h-6" />
                        </div>
                        <p className="text-sm font-bold text-gray-400 uppercase tracking-wider">Total Users</p>
                        <p className="text-3xl font-bold text-gray-900 mt-1">{stats?.total_users || 0}</p>
                    </div>
                </div>
            </div>

            {/* Placeholder for Charts or Recent Activity */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div className="bg-white p-8 rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/50 min-h-[400px] flex flex-col items-center justify-center text-gray-400 relative overflow-hidden">
                    <div className="absolute inset-0 bg-linear-to-br from-gray-50 to-transparent opacity-50" />
                    <TrendingUp className="w-16 h-16 mb-4 text-gray-300" />
                    <p className="font-semibold">Revenue Analytics</p>
                    <p className="text-xs">Coming to Phase 5</p>
                </div>
                <div className="bg-white p-8 rounded-3xl border border-gray-100 shadow-xl shadow-gray-200/50 min-h-[400px] flex flex-col items-center justify-center text-gray-400 relative overflow-hidden">
                    <div className="absolute inset-0 bg-linear-to-br from-gray-50 to-transparent opacity-50" />
                    <Users className="w-16 h-16 mb-4 text-gray-300" />
                    <p className="font-semibold">User Growth</p>
                    <p className="text-xs">Coming to Phase 5</p>
                </div>
            </div>
        </div>
    );
}
