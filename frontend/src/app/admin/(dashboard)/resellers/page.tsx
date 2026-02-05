"use client";

import { useEffect, useState } from "react";
import axiosInstance from "../../../../lib/axios";
import { toast } from "react-hot-toast";
import { Plus, Search, DollarSign, History, Loader2, X } from "lucide-react";
import { cn } from "../../../../lib/utils";

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    active: boolean;
    balance?: number; // Fetched separately
}

interface Deposit {
    id: number;
    amount: number;
    note: string;
    created_at: string;
}

export default function ResellerList() {
    const [resellers, setResellers] = useState<User[]>([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState("");

    // Modals
    const [depositModalUser, setDepositModalUser] = useState<User | null>(null);
    const [historyModalUser, setHistoryModalUser] = useState<User | null>(null);

    const fetchResellers = async () => {
        try {
            const res = await axiosInstance.get("/admin/users");
            // Filter client-side for now
            const allUsers: User[] = res.data || [];
            const resellerList = allUsers.filter(u => u.role === 'reseller');
            setResellers(resellerList);

            // Fetch balances (lazy or parallel)
            resellerList.forEach(r => fetchBalance(r.id));
        } catch (error) {
            toast.error("Failed to load resellers");
        } finally {
            setLoading(false);
        }
    };

    const fetchBalance = async (userId: number) => {
        try {
            const res = await axiosInstance.get(`/admin/finance/balance?user_id=${userId}`);
            setResellers(prev => prev.map(u => u.id === userId ? { ...u, balance: res.data.balance } : u));
        } catch (e) {
            console.error("Balance fetch failed", e);
        }
    };

    useEffect(() => {
        fetchResellers();
    }, []);

    const filtered = resellers.filter(u => u.name.toLowerCase().includes(search.toLowerCase()) || u.email.toLowerCase().includes(search.toLowerCase()));

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <div>
                    <h2 className="text-2xl font-bold text-(--foreground)">Resellers</h2>
                    <p className="text-gray-500 text-sm">Manage reseller accounts and deposits.</p>
                </div>
            </div>

            <div className="bg-(--card) p-4 rounded-xl border border-(--card-border) shadow-sm max-w-md">
                <div className="relative">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                    <input
                        type="text"
                        placeholder="Search resellers..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        className="w-full pl-10 pr-4 py-2 rounded-lg border border-(--card-border) bg-(--background) text-(--foreground) focus:border-blue-500 outline-none transition-all placeholder:opacity-50"
                    />
                </div>
            </div>

            <div className="bg-(--card) rounded-xl border border-(--card-border) shadow-sm overflow-hidden">
                <table className="w-full text-left">
                    <thead className="bg-(--background) text-xs text-gray-500 uppercase font-bold tracking-wider">
                        <tr>
                            <th className="px-6 py-4">Reseller</th>
                            <th className="px-6 py-4">Balance</th>
                            <th className="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-(--card-border)">
                        {filtered.map(user => (
                            <tr key={user.id} className="hover:bg-blue-600/5 transition-colors group">
                                <td className="px-6 py-4">
                                    <p className="font-bold text-(--foreground) group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{user.name}</p>
                                    <p className="text-xs text-gray-500">{user.email}</p>
                                </td>
                                <td className="px-6 py-4 font-mono font-bold text-green-600 dark:text-green-400">
                                    {user.balance !== undefined
                                        ? new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(user.balance)
                                        : '...'}
                                </td>
                                <td className="px-6 py-4 text-right">
                                    <div className="flex justify-end gap-2">
                                        <button
                                            onClick={() => setHistoryModalUser(user)}
                                            className="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-600/10 rounded-lg text-xs font-bold flex items-center gap-1 transition-all"
                                        >
                                            <History className="w-4 h-4" /> History
                                        </button>
                                        <button
                                            onClick={() => setDepositModalUser(user)}
                                            className="p-2 bg-green-500/10 text-green-600 dark:text-green-400 hover:bg-green-500/20 rounded-lg text-xs font-bold flex items-center gap-1 transition-all"
                                        >
                                            <DollarSign className="w-4 h-4" /> Add Deposit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                        {filtered.length === 0 && !loading && <tr><td colSpan={3} className="p-8 text-center text-gray-400">No resellers found.</td></tr>}
                    </tbody>
                </table>
            </div>

            {depositModalUser && (
                <DepositModal
                    user={depositModalUser}
                    onClose={() => setDepositModalUser(null)}
                    onSuccess={() => {
                        fetchBalance(depositModalUser.id);
                        setDepositModalUser(null);
                    }}
                />
            )}

            {historyModalUser && (
                <HistoryModal
                    user={historyModalUser}
                    onClose={() => setHistoryModalUser(null)}
                />
            )}
        </div>
    );
}

// --- SUB COMPONENTS ---

function DepositModal({ user, onClose, onSuccess }: { user: User, onClose: () => void, onSuccess: () => void }) {
    const [amount, setAmount] = useState("");
    const [note, setNote] = useState("");
    const [loading, setLoading] = useState(false);

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        setLoading(true);
        try {
            await axiosInstance.post("/admin/finance/deposits", {
                user_id: user.id,
                amount: Number(amount),
                note
            });
            toast.success("Deposit added!");
            onSuccess();
        } catch (e) {
            toast.error("Failed to add deposit");
        }
        finally {
            setLoading(false);
        }
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div className="bg-(--card) border border-(--card-border) rounded-2xl w-full max-w-md p-6 shadow-2xl animate-in fade-in zoom-in duration-200">
                <div className="flex justify-between items-center mb-4">
                    <h3 className="text-lg font-bold text-(--foreground)">Add Deposit</h3>
                    <button onClick={onClose}><X className="w-5 h-5 text-gray-400 hover:text-(--foreground) transition-colors" /></button>
                </div>
                <div className="mb-6 p-3 bg-blue-500/10 rounded-lg text-sm text-blue-600 dark:text-blue-400 font-medium">
                    Adding funds to <strong className="font-bold">{user.name}</strong>
                </div>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Amount (IDR)</label>
                        <input type="number" required min="1" className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) bg-(--background) text-(--foreground) focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all" value={amount} onChange={e => setAmount(e.target.value)} placeholder="0" />
                    </div>
                    <div>
                        <label className="block text-sm font-bold text-(--foreground) opacity-70 mb-2">Note (Optional)</label>
                        <input type="text" className="w-full px-4 py-2.5 rounded-lg border border-(--card-border) bg-(--background) text-(--foreground) focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all" value={note} onChange={e => setNote(e.target.value)} placeholder="e.g. Bank Transfer Ref..." />
                    </div>
                    <div className="flex gap-3 pt-2">
                        <button type="button" onClick={onClose} className="flex-1 py-2.5 rounded-lg border border-(--card-border) font-bold text-(--foreground) hover:bg-(--background) transition-all">Cancel</button>
                        <button type="submit" disabled={loading} className="flex-1 py-2.5 bg-green-600 text-white rounded-lg font-bold hover:bg-green-700 disabled:opacity-70 flex justify-center items-center gap-2 shadow-lg shadow-green-600/20">
                            {loading && <Loader2 className="w-4 h-4 animate-spin" />} Confirm
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

function HistoryModal({ user, onClose }: { user: User, onClose: () => void }) {
    const [deposits, setDeposits] = useState<Deposit[]>([]);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        axiosInstance.get(`/admin/finance/deposits?user_id=${user.id}`)
            .then((res: any) => setDeposits(res.data || []))
            .catch(() => toast.error("Failed to load history"))
            .finally(() => setLoading(false));
    }, [user.id]);

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4">
            <div className="bg-(--card) border border-(--card-border) rounded-2xl w-full max-w-lg p-6 shadow-2xl h-[500px] flex flex-col animate-in fade-in slide-in-from-bottom-4 duration-300">
                <div className="flex justify-between items-center mb-4">
                    <h3 className="text-lg font-bold text-(--foreground)">Deposit History: {user.name}</h3>
                    <button onClick={onClose}><X className="w-5 h-5 text-gray-400 hover:text-(--foreground) transition-colors" /></button>
                </div>

                <div className="flex-1 overflow-y-auto border border-(--card-border) rounded-xl bg-(--background)">
                    <table className="w-full text-sm text-left">
                        <thead className="bg-(--card) text-gray-500 sticky top-0 border-b border-(--card-border)">
                            <tr><th className="p-3">Date</th><th className="p-3">Note</th><th className="p-3 text-right">Amount</th></tr>
                        </thead>
                        <tbody className="divide-y divide-(--card-border)">
                            {loading ? (
                                <tr><td colSpan={3} className="p-8 text-center"><Loader2 className="w-6 h-6 animate-spin mx-auto text-gray-400" /></td></tr>
                            ) : deposits.length === 0 ? (
                                <tr><td colSpan={3} className="p-8 text-center text-gray-400">No deposits found.</td></tr>
                            ) : deposits.map(d => (
                                <tr key={d.id} className="hover:bg-blue-600/5 transition-colors">
                                    <td className="p-3 text-gray-500 font-medium">{new Date(d.created_at).toLocaleDateString()}</td>
                                    <td className="p-3 text-(--foreground)">{d.note || '-'}</td>
                                    <td className="p-3 text-right font-bold text-green-600 dark:text-green-400">+{new Intl.NumberFormat('id-ID').format(d.amount)}</td>
                                </tr>
                            ))}
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    );
}
