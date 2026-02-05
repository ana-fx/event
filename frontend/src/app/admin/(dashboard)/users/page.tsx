"use client";

import { useEffect, useState } from "react";
import Link from "next/link";
import { useSearchParams } from "next/navigation";
import axiosInstance from "../../../../lib/axios"; // Relative import fix
import { Plus, Search, Edit2, Trash2, Users as UsersIcon, Shield, Store, ScanBarcode } from "lucide-react";
import { toast } from "react-hot-toast";
import { Select } from "@/components/ui/Select";

interface User {
    id: number;
    name: string;
    email: string;
    role: string;
    is_active: boolean;
}

export default function UserList() {
    const searchParams = useSearchParams(); // Add this
    const roleParam = searchParams.get("role") || "all"; // Get query param

    const [users, setUsers] = useState<User[]>([]);
    const [loading, setLoading] = useState(true);
    const [search, setSearch] = useState("");
    const [roleFilter, setRoleFilter] = useState(roleParam); // Set initial state

    // content continues...
    const fetchUsers = async () => {
        try {
            const res = await axiosInstance.get("/admin/users");
            setUsers(res.data || []);
        } catch (error) {
            toast.error("Failed to load users");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        setRoleFilter(roleParam); // Helper to update when URL changes
    }, [roleParam]);

    useEffect(() => {
        fetchUsers();
    }, []);

    const handleDelete = async (id: number) => {
        if (!confirm("Are you sure? This cannot be undone.")) return;
        try {
            await axiosInstance.delete(`/admin/users?id=${id}`);
            toast.success("User deleted");
            fetchUsers();
        } catch (error) {
            toast.error("Failed to delete user");
        }
    };

    const filteredUsers = users.filter(user => {
        const matchesSearch = user.name.toLowerCase().includes(search.toLowerCase()) ||
            user.email.toLowerCase().includes(search.toLowerCase());
        const matchesRole = roleFilter === "all" || user.role === roleFilter;
        return matchesSearch && matchesRole;
    });

    const getRoleBadge = (role: string) => {
        switch (role) {
            case 'admin': return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-purple-500/10 text-purple-600 dark:text-purple-400"><Shield className="w-3 h-3" /> Admin</span>;
            case 'reseller': return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-500/10 text-blue-600 dark:text-blue-400"><Store className="w-3 h-3" /> Reseller</span>;
            case 'scanner': return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-orange-500/10 text-orange-600 dark:text-orange-400"><ScanBarcode className="w-3 h-3" /> Scanner</span>;
            default: return <span className="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-500/10 text-gray-600 dark:text-gray-400">User</span>;
        }
    };

    return (
        <div className="space-y-6">
            <div className="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 className="text-2xl font-bold text-(--foreground)">Users</h2>
                    <p className="text-gray-500 text-sm">Manage admins, resellers, and scanners.</p>
                </div>
                <Link
                    href="/admin/users/create"
                    className="px-4 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-sm shadow-blue-600/20 disabled:opacity-70 flex items-center justify-center"
                >
                    <Plus className="w-4 h-4 mr-2" />
                    Create User
                </Link>
            </div>

            {/* Filters */}
            <div className="bg-(--card) p-4 rounded-xl border border-(--card-border) shadow-sm flex flex-col md:flex-row gap-4">
                <div className="relative flex-1">
                    <Search className="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" />
                    <input
                        type="text"
                        placeholder="Search users..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        className="w-full pl-10 pr-4 py-2 rounded-lg border border-(--card-border) bg-(--background) text-(--foreground) focus:border-blue-500 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30 outline-none transition-all placeholder:opacity-50"
                    />
                </div>
                <div className="w-full md:w-48">
                    <Select
                        value={roleFilter}
                        onChange={(val) => setRoleFilter(val)}
                        className="py-2"
                        options={[
                            { label: "All Roles", value: "all" },
                            { label: "Admins", value: "admin" },
                            { label: "Resellers", value: "reseller" },
                            { label: "Scanners", value: "scanner" }
                        ]}
                    />
                </div>
            </div>

            {/* Table */}
            <div className="bg-(--card) rounded-xl border border-(--card-border) shadow-sm overflow-hidden">
                <table className="w-full text-left border-collapse">
                    <thead>
                        <tr className="bg-(--background) border-b border-(--card-border) text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th className="px-6 py-4">Name</th>
                            <th className="px-6 py-4">Role</th>
                            <th className="px-6 py-4">Status</th>
                            <th className="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody className="divide-y divide-(--card-border)">
                        {loading ? (
                            <tr><td colSpan={4} className="p-8 text-center text-gray-400">Loading...</td></tr>
                        ) : filteredUsers.length === 0 ? (
                            <tr><td colSpan={4} className="p-8 text-center text-gray-400">No users found.</td></tr>
                        ) : (
                            filteredUsers.map(user => (
                                <tr key={user.id} className="hover:bg-blue-600/5 transition-colors group">
                                    <td className="px-6 py-4">
                                        <div className="flex items-center gap-3">
                                            <div className="w-10 h-10 rounded-full bg-(--background) flex items-center justify-center text-gray-500 font-bold border border-(--card-border)">
                                                {user.name[0].toUpperCase()}
                                            </div>
                                            <div>
                                                <p className="font-bold text-(--foreground) group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{user.name}</p>
                                                <p className="text-xs text-gray-500">{user.email}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td className="px-6 py-4">
                                        {getRoleBadge(user.role)}
                                    </td>
                                    <td className="px-6 py-4">
                                        <span className={`inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold ${user.is_active ? 'bg-green-500/10 text-green-600' : 'bg-red-500/10 text-red-600'
                                            }`}>
                                            {user.is_active ? 'Active' : 'Inactive'}
                                        </span>
                                    </td>
                                    <td className="px-6 py-4 text-right">
                                        <div className="flex items-center justify-end gap-2 text-(--foreground)">
                                            <Link href={`/admin/users/edit?id=${user.id}`} className="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-600/10 rounded-lg transition-colors">
                                                <Edit2 className="w-4 h-4" />
                                            </Link>
                                            <button onClick={() => handleDelete(user.id)} className="p-2 text-gray-400 hover:text-red-600 hover:bg-red-600/10 rounded-lg transition-colors">
                                                <Trash2 className="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            ))
                        )}
                    </tbody>
                </table>
            </div>
        </div>
    );
}
