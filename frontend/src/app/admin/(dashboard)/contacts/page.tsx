"use client";

import { useEffect, useState } from "react";
import axiosInstance from "@/lib/axios";
import { toast } from "react-hot-toast";
import { Trash, MessageSquare, Loader2 } from "lucide-react";

interface Contact {
    id: number;
    name: string;
    email: string;
    message: string;
    created_at: string;
}

export default function ContactsPage() {
    const [contacts, setContacts] = useState<Contact[]>([]);
    const [loading, setLoading] = useState(true);

    const fetchContacts = async () => {
        try {
            const res = await axiosInstance.get("/admin/contacts");
            setContacts(res.data || []);
        } catch (error) {
            toast.error("Failed to load messages");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => { fetchContacts(); }, []);

    const handleDelete = async (id: number) => {
        if (!confirm("Are you sure?")) return;
        try {
            await axiosInstance.delete(`/admin/contacts?id=${id}`);
            toast.success("Message deleted");
            setContacts(prev => prev.filter(c => c.id !== id));
        } catch (e) { toast.error("Failed to delete message"); }
    };

    return (
        <div className="space-y-6">
            <div>
                <h2 className="text-2xl font-bold text-gray-900">Contact Messages</h2>
                <p className="text-gray-500 text-sm">View inquiries from the contact form.</p>
            </div>

            <div className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden">
                <div className="divide-y divide-gray-100">
                    {contacts.map(c => (
                        <div key={c.id} className="p-6 hover:bg-gray-50 transition-colors">
                            <div className="flex justify-between items-start mb-2">
                                <div>
                                    <h3 className="font-bold text-gray-900">{c.name}</h3>
                                    <p className="text-sm text-gray-500">{c.email}</p>
                                </div>
                                <span className="text-xs text-gray-400">{new Date(c.created_at).toLocaleDateString()}</span>
                            </div>
                            <p className="text-gray-700 mt-3 whitespace-pre-wrap leading-relaxed">{c.message}</p>
                            <div className="flex justify-end mt-4">
                                <button onClick={() => handleDelete(c.id)} className="text-red-500 hover:text-red-700 text-sm font-medium flex items-center gap-1">
                                    <Trash className="w-4 h-4" /> Delete
                                </button>
                            </div>
                        </div>
                    ))}
                    {contacts.length === 0 && !loading && (
                        <div className="text-center py-12">
                            <MessageSquare className="w-12 h-12 mx-auto text-gray-300 mb-2" />
                            <p className="text-gray-500">No messages yet.</p>
                        </div>
                    )}
                </div>
            </div>
        </div>
    );
}
