"use client";

import { useState } from "react";
import { use } from "react";
import axiosInstance from "@/lib/axios";
import { toast } from "react-hot-toast";
import {
    Search, CheckCircle, XCircle, ArrowLeft,
    User, Mail, Ticket, AlertTriangle,
} from "lucide-react";
import Link from "next/link";

interface VerifyResult {
    valid: boolean;
    msg: string;
    quantity?: number;
    data?: {
        ID: number;
        Name: string;
        Email: string;
        Status: string;
    };
    items?: { ID: number; Name: string; Quantity: number }[];
}

type ScanState = "idle" | "verified" | "redeemed" | "error";

export default function ScanPage({ params }: { params: Promise<{ eventId: string }> }) {
    const { eventId } = use(params);

    const [code, setCode] = useState("");
    const [loading, setLoading] = useState(false);
    const [scanState, setScanState] = useState<ScanState>("idle");
    const [verifyResult, setVerifyResult] = useState<VerifyResult | null>(null);
    const [errorMsg, setErrorMsg] = useState("");

    const handleVerify = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!code.trim()) return;

        setLoading(true);
        setScanState("idle");
        setVerifyResult(null);
        setErrorMsg("");

        try {
            const res = await axiosInstance.post("/scanner/verify", {
                code: code.trim(),
                event_id: Number(eventId),
            });
            const data: VerifyResult = res.data;
            setVerifyResult(data);

            if (data.valid) {
                setScanState("verified");
            } else {
                setScanState("error");
                setErrorMsg(data.msg);
                toast.error(data.msg);
            }
        } catch (err: any) {
            const msg = err.response?.data || "Tiket tidak ditemukan";
            setScanState("error");
            setErrorMsg(msg);
            toast.error(msg);
        } finally {
            setLoading(false);
        }
    };

    const handleRedeem = async () => {
        setLoading(true);
        try {
            await axiosInstance.post("/scanner/redeem", {
                code: code.trim(),
                event_id: Number(eventId),
            });
            setScanState("redeemed");
            toast.success("Tiket berhasil di-redeem!");
        } catch (err: any) {
            const msg = err.response?.data || "Gagal redeem tiket";
            setScanState("error");
            setErrorMsg(msg);
            toast.error(msg);
        } finally {
            setLoading(false);
        }
    };

    const handleReset = () => {
        setCode("");
        setScanState("idle");
        setVerifyResult(null);
        setErrorMsg("");
    };

    return (
        <div className="p-6 max-w-md mx-auto">
            {/* Back */}
            <Link
                href="/scanner"
                className="inline-flex items-center gap-1.5 text-gray-400 hover:text-white text-sm mb-6 transition-colors"
            >
                <ArrowLeft className="w-4 h-4" /> Kembali ke Dashboard
            </Link>

            <h1 className="text-xl font-bold text-white mb-6">Scan Tiket</h1>

            {/* Input Form */}
            {(scanState === "idle" || scanState === "error") && (
                <form onSubmit={handleVerify} className="relative mb-4">
                    <input
                        type="text"
                        value={code}
                        onChange={(e) => setCode(e.target.value)}
                        placeholder="Masukkan kode tiket..."
                        className="w-full bg-gray-800 border border-gray-700 rounded-2xl py-4 pl-6 pr-14 text-base focus:ring-2 focus:ring-primary outline-none transition-all placeholder:text-gray-500 text-white"
                        autoFocus
                        autoComplete="off"
                    />
                    <button
                        type="submit"
                        disabled={loading || !code.trim()}
                        className="absolute right-2 top-2 bottom-2 bg-primary text-white p-3 rounded-xl transition-colors disabled:opacity-50"
                    >
                        {loading
                            ? <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin" />
                            : <Search className="w-5 h-5" />
                        }
                    </button>
                </form>
            )}

            {scanState === "error" && (
                <p className="text-sm text-gray-400 text-center mb-2">Ketik atau scan kode berikutnya</p>
            )}

            {/* Error Result */}
            {scanState === "error" && (
                <div className="bg-red-950/60 border border-red-800 rounded-2xl p-6 text-center animate-in fade-in zoom-in duration-200">
                    <XCircle className="w-14 h-14 text-red-400 mx-auto mb-3" />
                    <p className="text-red-300 font-semibold text-lg">{errorMsg}</p>
                </div>
            )}

            {/* Verified — show detail + confirm redeem */}
            {scanState === "verified" && verifyResult?.data && (
                <div className="bg-gray-900 border border-gray-700 rounded-2xl overflow-hidden animate-in fade-in zoom-in duration-200">
                    {/* Status bar */}
                    <div className="bg-blue-600 px-4 py-3 flex items-center gap-2">
                        <Ticket className="w-5 h-5 text-white" />
                        <span className="text-white font-semibold">Tiket Valid — Konfirmasi Masuk</span>
                    </div>

                    {/* Ticket info */}
                    <div className="p-5 space-y-3">
                        <div className="flex items-center gap-3">
                            <User className="w-4 h-4 text-gray-400 shrink-0" />
                            <div>
                                <p className="text-xs text-gray-500">Nama Pemegang</p>
                                <p className="text-white font-medium">{verifyResult.data.Name}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-3">
                            <Mail className="w-4 h-4 text-gray-400 shrink-0" />
                            <div>
                                <p className="text-xs text-gray-500">Email</p>
                                <p className="text-white">{verifyResult.data.Email}</p>
                            </div>
                        </div>
                        <div className="flex items-center gap-3">
                            <Ticket className="w-4 h-4 text-gray-400 shrink-0" />
                            <div>
                                <p className="text-xs text-gray-500">Jumlah Tiket</p>
                                <p className="text-white">{verifyResult.quantity ?? 1} tiket</p>
                            </div>
                        </div>
                        {verifyResult.items && verifyResult.items.length > 0 && (
                            <div className="border-t border-gray-800 pt-3">
                                <p className="text-xs text-gray-500 mb-2">Detail Tiket</p>
                                <ul className="space-y-1">
                                    {verifyResult.items.map((item) => (
                                        <li key={item.ID} className="flex justify-between text-sm text-gray-300">
                                            <span>{item.Name}</span>
                                            <span>{item.Quantity}x</span>
                                        </li>
                                    ))}
                                </ul>
                            </div>
                        )}
                    </div>

                    {/* Actions */}
                    <div className="px-5 pb-5 flex gap-3">
                        <button
                            onClick={handleReset}
                            className="flex-1 py-3 rounded-xl border border-gray-700 text-gray-300 hover:bg-gray-800 transition-colors font-medium"
                        >
                            Batal
                        </button>
                        <button
                            onClick={handleRedeem}
                            disabled={loading}
                            className="flex-1 py-3 rounded-xl bg-green-600 hover:bg-green-500 text-white font-bold transition-colors disabled:opacity-50"
                        >
                            {loading
                                ? <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin mx-auto" />
                                : "Izinkan Masuk"
                            }
                        </button>
                    </div>
                </div>
            )}

            {/* Redeemed success */}
            {scanState === "redeemed" && (
                <div className="bg-green-950/60 border border-green-800 rounded-2xl p-8 text-center animate-in fade-in zoom-in duration-200">
                    <CheckCircle className="w-20 h-20 text-green-400 mx-auto mb-4" />
                    <h2 className="text-2xl font-bold text-white mb-1">Masuk Diizinkan</h2>
                    {verifyResult?.data && (
                        <p className="text-green-300">{verifyResult.data.Name}</p>
                    )}
                    <button
                        onClick={handleReset}
                        className="mt-6 w-full bg-white text-gray-900 py-3 rounded-xl font-bold hover:bg-gray-100 transition-colors"
                    >
                        Scan Berikutnya
                    </button>
                </div>
            )}

            {/* Warning for already redeemed (msg from verify) */}
            {scanState === "error" && errorMsg.toLowerCase().includes("already") && (
                <div className="flex items-start gap-2 bg-yellow-900/30 border border-yellow-700/50 rounded-xl p-3 mt-3">
                    <AlertTriangle className="w-4 h-4 text-yellow-400 mt-0.5 shrink-0" />
                    <p className="text-yellow-300 text-sm">Tiket ini sudah pernah digunakan sebelumnya.</p>
                </div>
            )}
        </div>
    );
}
