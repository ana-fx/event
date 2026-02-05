"use client";

import { useEffect, useState } from "react";
import axiosInstance from "@/lib/axios";
import { toast } from "react-hot-toast";
import { Wallet, History, CreditCard, LogOut, Loader2 } from "lucide-react";
import Cookies from "js-cookie";
import { useRouter } from "next/navigation";

interface Transaction {
    id: number;
    type: string;
    amount: number;
    description: string;
    created_at: string;
}

export default function ResellerDashboard() {
    const [balance, setBalance] = useState<number | null>(null);
    const [transactions, setTransactions] = useState<Transaction[]>([]);
    const [loading, setLoading] = useState(true);
    const router = useRouter();

    useEffect(() => {
        const fetchData = async () => {
            try {
                // Get User ID from cookie (or decoding token would be better, but cookie is simpler here)
                // Actually backend /balance requires user_id query param for ADMIN, 
                // but usually a logged in user should be able to get THEIR OWN balance.
                // Let's assume the backend endpoint handles "me" or we pass the ID from stored cookie.
                const userStr = Cookies.get("user");
                if (!userStr) { router.push("/admin/login"); return; }
                const user = JSON.parse(userStr);

                // Assuming we reuse the admin endpoint but maybe it checks permission?
                // The handler `GetBalance` retrieves `user_id` from Query.
                // Ideally we should have a `/user/balance` endpoint.
                // For now, let's try passing our own ID.
                const balanceRes = await axiosInstance.get(`/admin/finance/balance?user_id=${user.id}`);
                setBalance(balanceRes.data.balance);

                // Transactions
                // Backend `GetReports` is for all. We might need a specific endpoint or filter.
                // Actually `test_phase2.ps1` accessed `/admin/finance/deposits`.
                // Let's check `finance.go`.
                // `GetDeposits` filters by `user_id`. So we can use that for history.
                const historyRes = await axiosInstance.get(`/admin/finance/deposits?user_id=${user.id}`);
                setTransactions(historyRes.data || []);

            } catch (error) {
                console.error(error);
                toast.error("Failed to load dashboard data");
            } finally {
                setLoading(false);
            }
        };

        fetchData();
    }, [router]);

    const handleLogout = () => {
        Cookies.remove("token");
        Cookies.remove("user");
        router.push("/admin/login");
    };

    if (loading) return <div className="min-h-screen flex items-center justify-center bg-gray-50"><Loader2 className="w-8 h-8 animate-spin text-blue-600" /></div>;

    return (
        <div className="min-h-screen bg-gray-50">
            {/* Header */}
            <div className="bg-white border-b border-gray-200 px-6 py-4 flex justify-between items-center sticky top-0 z-10">
                <div className="flex items-center gap-3">
                    <div className="bg-blue-600 text-white p-2 rounded-lg"><Wallet className="w-5 h-5" /></div>
                    <h1 className="font-bold text-xl text-gray-900">Reseller<span className="text-blue-600">Portal</span></h1>
                </div>
                <button onClick={handleLogout} className="text-gray-500 hover:text-red-600 transition-colors"><LogOut className="w-5 h-5" /></button>
            </div>

            <div className="max-w-4xl mx-auto p-6 space-y-8">
                {/* Balance Card */}
                <div className="bg-linear-to-br from-blue-600 to-blue-800 rounded-3xl p-8 text-white shadow-xl shadow-blue-600/20 relative overflow-hidden">
                    <div className="relative z-10">
                        <p className="text-blue-100 font-medium mb-1">Current Balance</p>
                        <h2 className="text-5xl font-bold">
                            {new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(balance || 0)}
                        </h2>
                    </div>
                    <Wallet className="absolute right-[-20px] bottom-[-20px] w-64 h-64 text-white/5 rotate-[-15deg]" />
                </div>

                {/* Quick Actions (Future) */}
                {/* <div className="grid grid-cols-2 gap-4">
                    <button className="bg-white p-4 rounded-xl shadow-sm border border-gray-100 hover:border-blue-200 transition-all flex flex-col items-center justify-center gap-2 font-bold text-gray-700">
                        <CreditCard className="w-6 h-6 text-blue-600" /> Top Up
                    </button>
                    ...
                </div> */}

                {/* History */}
                <div>
                    <h3 className="font-bold text-lg text-gray-900 mb-4 flex items-center gap-2">
                        <History className="w-5 h-5 text-gray-500" /> Transaction History
                    </h3>
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                        {transactions.length === 0 ? (
                            <div className="p-8 text-center text-gray-400">No transactions found</div>
                        ) : (
                            <table className="w-full text-left text-sm">
                                <thead className="bg-gray-50 text-gray-500">
                                    <tr>
                                        <th className="p-4 font-medium">Date</th>
                                        <th className="p-4 font-medium">Description</th>
                                        <th className="p-4 font-medium text-right">Amount</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100">
                                    {transactions.map((tx) => (
                                        <tr key={tx.id} className="hover:bg-gray-50/50 transition-colors">
                                            <td className="p-4 text-gray-500">{new Date(tx.created_at).toLocaleDateString()}</td>
                                            <td className="p-4 font-medium text-gray-900">{tx.description || tx.type || "Deposit"}</td>
                                            <td className="p-4 text-right font-bold text-green-600">
                                                +{new Intl.NumberFormat('id-ID').format(tx.amount)}
                                            </td>
                                        </tr>
                                    ))}
                                </tbody>
                            </table>
                        )}
                    </div>
                </div>
            </div>
        </div>
    );
}
