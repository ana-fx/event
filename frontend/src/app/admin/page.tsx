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
        <div className="space-y-8">
            <div>
                <h2 className="text-3xl font-bold text-gray-900">Dashboard</h2>
                <p className="text-gray-500 mt-1">Overview of your event platform performance.</p>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {/* Total Revenue */}
                <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div className="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center text-green-600">
                        <DollarSign className="w-6 h-6" />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-gray-500">Total Revenue</p>
                        <p className="text-2xl font-bold text-gray-900">
                            {new Intl.NumberFormat("id-ID", { style: "currency", currency: "IDR" }).format(stats?.total_revenue || 0)}
                        </p>
                    </div>
                </div>

                {/* Tickets Sold */}
                <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div className="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center text-blue-600">
                        <Ticket className="w-6 h-6" />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-gray-500">Tickets Sold</p>
                        <p className="text-2xl font-bold text-gray-900">{stats?.tickets_sold || 0}</p>
                    </div>
                </div>

                {/* Active Events */}
                <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div className="w-12 h-12 rounded-xl bg-purple-100 flex items-center justify-center text-purple-600">
                        <Calendar className="w-6 h-6" />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-gray-500">Active Events</p>
                        <p className="text-2xl font-bold text-gray-900">{stats?.active_events || 0}</p>
                    </div>
                </div>

                {/* Total Users */}
                <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm flex items-center gap-4">
                    <div className="w-12 h-12 rounded-xl bg-orange-100 flex items-center justify-center text-orange-600">
                        <Users className="w-6 h-6" />
                    </div>
                    <div>
                        <p className="text-sm font-medium text-gray-500">Total Users</p>
                        <p className="text-2xl font-bold text-gray-900">{stats?.total_users || 0}</p>
                    </div>
                </div>
            </div>

            {/* Placeholder for Charts or Recent Activity */}
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm min-h-[300px] flex flex-col items-center justify-center text-gray-400">
                    <TrendingUp className="w-12 h-12 mb-4 opacity-50" />
                    <p>Revenue Chart Coming Soon</p>
                </div>
                <div className="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm min-h-[300px] flex flex-col items-center justify-center text-gray-400">
                    <Users className="w-12 h-12 mb-4 opacity-50" />
                    <p>Recent Signups Coming Soon</p>
                </div>
            </div>
        </div>
    );
}
