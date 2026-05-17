"use client";

import { useEffect, useState } from "react";
import axiosInstance from "@/lib/axios";
import { ScanBarcode, Plus, Trash2, Eye, EyeOff, X } from "lucide-react";
import toast from "react-hot-toast";

interface Scanner {
    id: number;
    name: string;
    email: string;
    is_active: boolean;
}

export default function OrganizerScannersPage() {
    const [scanners, setScanners] = useState<Scanner[]>([]);
    const [loading, setLoading] = useState(true);
    const [showModal, setShowModal] = useState(false);
    const [showPassword, setShowPassword] = useState(false);
    const [form, setForm] = useState({ name: "", email: "", password: "" });
    const [submitting, setSubmitting] = useState(false);

    const fetchScanners = async () => {
        setLoading(true);
        try {
            const res = await axiosInstance.get("/organizer/scanners");
            setScanners(res.data || []);
        } catch {
            toast.error("Gagal memuat daftar scanner");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => { fetchScanners(); }, []);

    const handleCreate = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!form.name || !form.email || !form.password) {
            toast.error("Semua field wajib diisi");
            return;
        }
        setSubmitting(true);
        try {
            await axiosInstance.post("/organizer/scanners", form);
            toast.success("Scanner berhasil dibuat");
            setShowModal(false);
            setForm({ name: "", email: "", password: "" });
            fetchScanners();
        } catch (err: any) {
            toast.error(err?.response?.data || "Gagal membuat scanner");
        } finally {
            setSubmitting(false);
        }
    };

    const handleDelete = async (id: number, name: string) => {
        if (!confirm(`Hapus scanner "${name}"?`)) return;
        try {
            await axiosInstance.delete(`/organizer/scanners?id=${id}`);
            toast.success("Scanner dihapus");
            fetchScanners();
        } catch {
            toast.error("Gagal menghapus scanner");
        }
    };

    return (
        <div className="max-w-3xl mx-auto">
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-(--foreground)">Manajemen Scanner</h1>
                    <p className="text-sm text-gray-500 mt-1">Buat dan kelola akun scanner untuk event Anda</p>
                </div>
                <button
                    onClick={() => setShowModal(true)}
                    className="flex items-center gap-2 px-5 py-2.5 bg-primary text-white font-bold rounded-xl hover:bg-primary/90 transition-all shadow-lg shadow-primary/30 active:scale-95"
                >
                    <Plus className="w-4 h-4" />
                    Buat Scanner
                </button>
            </div>

            {loading ? (
                <div className="flex items-center justify-center py-20">
                    <div className="animate-spin rounded-full h-8 w-8 border-b-2 border-primary" />
                </div>
            ) : scanners.length === 0 ? (
                <div className="text-center py-20 bg-(--card) rounded-3xl border-2 border-dashed border-(--card-border)">
                    <ScanBarcode className="w-12 h-12 text-gray-300 mx-auto mb-3" />
                    <p className="text-gray-500 font-bold">Belum ada scanner</p>
                    <p className="text-xs text-gray-400 mt-1">Klik "Buat Scanner" untuk menambahkan akun scanner</p>
                </div>
            ) : (
                <div className="space-y-3">
                    {scanners.map(s => (
                        <div key={s.id} className="flex items-center justify-between p-5 bg-(--card) border border-(--card-border) rounded-2xl hover:border-primary/40 transition-all">
                            <div className="flex items-center gap-4">
                                <div className="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold">
                                    {s.name.charAt(0).toUpperCase()}
                                </div>
                                <div>
                                    <p className="font-semibold text-(--foreground)">{s.name}</p>
                                    <p className="text-sm text-gray-500">{s.email}</p>
                                </div>
                            </div>
                            <div className="flex items-center gap-3">
                                <span className={`text-xs px-2.5 py-1 rounded-full font-bold ${s.is_active ? "bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400" : "bg-red-100 text-red-700"}`}>
                                    {s.is_active ? "Aktif" : "Nonaktif"}
                                </span>
                                <button
                                    onClick={() => handleDelete(s.id, s.name)}
                                    className="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-all"
                                    title="Hapus scanner"
                                >
                                    <Trash2 className="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    ))}
                </div>
            )}

            {/* Create Modal */}
            {showModal && (
                <div
                    className="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
                    onClick={() => setShowModal(false)}
                >
                    <div
                        className="bg-(--card) rounded-3xl w-full max-w-md shadow-2xl border border-(--card-border)"
                        onClick={e => e.stopPropagation()}
                    >
                        <div className="flex items-center justify-between p-6 border-b border-(--card-border)">
                            <h2 className="text-lg font-bold text-(--foreground)">Buat Akun Scanner</h2>
                            <button onClick={() => setShowModal(false)} className="p-2 hover:bg-(--background) rounded-xl transition-colors">
                                <X className="w-5 h-5 text-gray-400" />
                            </button>
                        </div>

                        <form onSubmit={handleCreate} className="p-6 space-y-4">
                            <div>
                                <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Nama</label>
                                <input
                                    type="text"
                                    value={form.name}
                                    onChange={e => setForm(f => ({ ...f, name: e.target.value }))}
                                    className="w-full px-4 py-3 bg-(--background) border border-(--card-border) rounded-xl text-(--foreground) focus:outline-none focus:border-primary transition-colors"
                                    placeholder="Nama scanner"
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Email</label>
                                <input
                                    type="email"
                                    value={form.email}
                                    onChange={e => setForm(f => ({ ...f, email: e.target.value }))}
                                    className="w-full px-4 py-3 bg-(--background) border border-(--card-border) rounded-xl text-(--foreground) focus:outline-none focus:border-primary transition-colors"
                                    placeholder="email@scanner.com"
                                />
                            </div>
                            <div>
                                <label className="block text-xs font-bold text-gray-500 uppercase tracking-wider mb-1.5">Password</label>
                                <div className="relative">
                                    <input
                                        type={showPassword ? "text" : "password"}
                                        value={form.password}
                                        onChange={e => setForm(f => ({ ...f, password: e.target.value }))}
                                        className="w-full px-4 py-3 bg-(--background) border border-(--card-border) rounded-xl text-(--foreground) focus:outline-none focus:border-primary transition-colors pr-12"
                                        placeholder="Min. 6 karakter"
                                    />
                                    <button
                                        type="button"
                                        onClick={() => setShowPassword(v => !v)}
                                        className="absolute right-3 top-1/2 -translate-y-1/2 p-1.5 text-gray-400 hover:text-gray-600"
                                    >
                                        {showPassword ? <EyeOff className="w-4 h-4" /> : <Eye className="w-4 h-4" />}
                                    </button>
                                </div>
                            </div>

                            <div className="flex gap-3 pt-2">
                                <button
                                    type="button"
                                    onClick={() => setShowModal(false)}
                                    className="flex-1 py-3 border border-(--card-border) rounded-xl font-bold text-gray-500 hover:bg-(--background) transition-colors"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={submitting}
                                    className="flex-1 py-3 bg-primary text-white rounded-xl font-bold hover:bg-primary/90 transition-all disabled:opacity-60"
                                >
                                    {submitting ? "Menyimpan..." : "Buat Scanner"}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </div>
    );
}
