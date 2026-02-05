"use client";

import { useState } from "react";
import axiosInstance from "@/lib/axios";
import { toast } from "react-hot-toast";
import { QrCode, CheckCircle, XCircle, Search, LogOut } from "lucide-react";
import Cookies from "js-cookie";
import { useRouter } from "next/navigation";

export default function ScannerPortal() {
    const [code, setCode] = useState("");
    const [loading, setLoading] = useState(false);
    const [result, setResult] = useState<any>(null);
    const router = useRouter();

    const handleScan = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!code) return;
        setLoading(true);
        setResult(null);

        try {
            // First verify
            const verifyRes = await axiosInstance.post("/tickets/verify", { code });
            const ticket = verifyRes.data;

            if (ticket.status === 'valid') {
                // If valid, auto-redeem? Or ask for confirmation?
                // Let's simple redeem for now or just show valid.
                // Requirement: "Scan/Verify Ticket Interface". usually implies redeeming entry.
                // Let's call redeem.
                try {
                    await axiosInstance.post("/tickets/redeem", { code });
                    setResult({ status: "success", message: "Ticket Valid & Redeemed", ticket });
                    toast.success("Entry Allowed");
                } catch (redeemErr: any) {
                    setResult({ status: "error", message: redeemErr.response?.data?.error || "Failed to redeem" });
                }
            } else {
                setResult({ status: "error", message: "Ticket is " + ticket.status, ticket });
                toast.error("Ticket Invalid");
            }
        } catch (error: any) {
            setResult({ status: "error", message: error.response?.data?.error || "Invalid Code" });
            toast.error("Invalid Code");
        } finally {
            setLoading(false);
        }
    };

    const handleLogout = () => {
        Cookies.remove("token");
        Cookies.remove("user");
        router.push("/admin/login");
    };

    return (
        <div className="min-h-screen bg-gray-900 text-white flex flex-col">
            {/* Header */}
            <div className="p-4 flex justify-between items-center bg-gray-800 shadow-md">
                <h1 className="font-bold text-lg flex items-center gap-2">
                    <QrCode className="text-blue-400" /> Scanner Portal
                </h1>
                <button onClick={handleLogout} className="text-gray-400 hover:text-white"><LogOut className="w-5 h-5" /></button>
            </div>

            {/* Main Content */}
            <div className="flex-1 flex flex-col items-center justify-center p-6 space-y-8">

                <div className="w-full max-w-sm">
                    <form onSubmit={handleScan} className="relative">
                        <input
                            type="text"
                            value={code}
                            onChange={(e) => setCode(e.target.value)}
                            placeholder="Enter Ticket Code"
                            className="w-full bg-gray-800 border border-gray-700 rounded-2xl py-4 pl-6 pr-14 text-lg focus:ring-2 focus:ring-blue-500 outline-none transition-all placeholder:text-gray-500"
                            autoFocus
                        />
                        <button
                            type="submit"
                            disabled={loading || !code}
                            className="absolute right-2 top-2 bottom-2 bg-blue-600 hover:bg-blue-500 text-white p-3 rounded-xl transition-colors disabled:opacity-50"
                        >
                            {loading ? <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" /> : <Search className="w-5 h-5" />}
                        </button>
                    </form>
                    <p className="text-center text-gray-500 text-sm mt-4">Type code or use external scanner</p>
                </div>

                {/* Result Card */}
                {result && (
                    <div className={`w-full max-w-sm p-8 rounded-3xl text-center animate-in fade-in zoom-in duration-300 ${result.status === 'success' ? 'bg-green-600' : 'bg-red-600'}`}>
                        {result.status === 'success' ? (
                            <CheckCircle className="w-20 h-20 mx-auto text-white/90 mb-4" />
                        ) : (
                            <XCircle className="w-20 h-20 mx-auto text-white/90 mb-4" />
                        )}
                        <h2 className="text-2xl font-bold mb-2">{result.message}</h2>
                        {result.ticket && (
                            <div className="text-white/80 mt-2 text-sm">
                                <p>{result.ticket.event_name}</p>
                                <p className="font-mono mt-1">{result.ticket.code}</p>
                                {result.ticket.holder_name && <p className="mt-1 font-bold">{result.ticket.holder_name}</p>}
                            </div>
                        )}
                        <button onClick={() => { setResult(null); setCode(""); }} className="mt-6 bg-white text-gray-900 px-6 py-3 rounded-xl font-bold hover:bg-gray-100 transition-colors w-full">
                            Scan Next
                        </button>
                    </div>
                )}

            </div>
        </div>
    );
}
