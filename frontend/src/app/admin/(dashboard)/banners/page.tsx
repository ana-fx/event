"use client";

import { useEffect, useState } from "react";
import axiosInstance from "@/lib/axios";
import { toast } from "react-hot-toast";
import { Plus, Trash, Loader2, Link as LinkIcon, Eye, Image as ImageIcon } from "lucide-react";
import Image from "next/image";

interface Banner {
    id: number;
    title: string;
    image_path: string;
    link_url: string;
    is_active: boolean;
}

export default function BannersPage() {
    const [banners, setBanners] = useState<Banner[]>([]);
    const [loading, setLoading] = useState(true);
    const [isModalOpen, setIsModalOpen] = useState(false);

    const fetchBanners = async () => {
        try {
            const res = await axiosInstance.get("/admin/banners");
            setBanners(res.data || []);
        } catch (error) {
            toast.error("Failed to load banners");
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => { fetchBanners(); }, []);

    const handleDelete = async (id: number) => {
        if (!confirm("Are you sure you want to delete this banner?")) return;
        try {
            await axiosInstance.delete(`/admin/banners?id=${id}`);
            toast.success("Banner deleted");
            fetchBanners();
        } catch (e) { toast.error("Failed to delete banner"); }
    };

    return (
        <div className="space-y-6">
            <div className="flex justify-between items-center">
                <div>
                    <h2 className="text-2xl font-bold text-gray-900">Banners</h2>
                    <p className="text-gray-500 text-sm">Manage homepage carousel banners.</p>
                </div>
                <button
                    onClick={() => setIsModalOpen(true)}
                    className="px-4 py-2.5 bg-blue-600 text-white font-bold rounded-lg hover:bg-blue-700 transition-colors shadow-sm shadow-blue-600/20 flex items-center justify-center"
                >
                    <Plus className="w-5 h-5 mr-2" />
                    Add Banner
                </button>
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {banners.map(banner => (
                    <div key={banner.id} className="bg-white rounded-xl border border-gray-100 shadow-sm overflow-hidden group">
                        <div className="relative aspect-video bg-gray-50">
                            {banner.image_path ? (
                                <img
                                    src={`${process.env.NEXT_PUBLIC_API_URL?.replace('/api', '')}${banner.image_path}`}
                                    alt={banner.title}
                                    className="w-full h-full object-cover"
                                />
                            ) : (
                                <div className="flex items-center justify-center h-full text-gray-300"><ImageIcon className="w-12 h-12" /></div>
                            )}
                            <div className="absolute top-2 right-2 flex gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button onClick={() => handleDelete(banner.id)} className="p-2 bg-white/90 text-red-600 rounded-lg hover:bg-red-50 shadow-sm">
                                    <Trash className="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                        <div className="p-4">
                            <h3 className="font-bold text-gray-900 truncate">{banner.title}</h3>
                            {banner.link_url && (
                                <div className="flex items-center text-sm text-blue-600 mt-1 truncate">
                                    <LinkIcon className="w-3 h-3 mr-1" />
                                    <a href={banner.link_url} target="_blank" rel="noopener noreferrer" className="hover:underline">{banner.link_url}</a>
                                </div>
                            )}
                        </div>
                    </div>
                ))}
            </div>

            {banners.length === 0 && !loading && (
                <div className="text-center py-12 bg-gray-50 rounded-xl border border-dashed border-gray-200">
                    <ImageIcon className="w-12 h-12 mx-auto text-gray-300 mb-2" />
                    <p className="text-gray-500">No banners found. Create your first one!</p>
                </div>
            )}

            {isModalOpen && <CreateBannerModal onClose={() => setIsModalOpen(false)} onSuccess={fetchBanners} />}
        </div>
    );
}

function CreateBannerModal({ onClose, onSuccess }: { onClose: () => void, onSuccess: () => void }) {
    const [title, setTitle] = useState("");
    const [link, setLink] = useState("");
    const [file, setFile] = useState<File | null>(null);
    const [preview, setPreview] = useState<string>("");
    const [loading, setLoading] = useState(false);

    const handleFile = (e: React.ChangeEvent<HTMLInputElement>) => {
        const f = e.target.files?.[0];
        if (f) {
            setFile(f);
            setPreview(URL.createObjectURL(f));
        }
    };

    const handleSubmit = async (e: React.FormEvent) => {
        e.preventDefault();
        if (!file) { toast.error("Image is required"); return; }

        setLoading(true);
        const data = new FormData();
        data.append("title", title);
        data.append("link_url", link);
        data.append("image", file);

        try {
            await axiosInstance.post("/admin/banners", data, {
                headers: { "Content-Type": "multipart/form-data" }
            });
            toast.success("Banner created!");
            onSuccess();
            onClose();
        } catch (e) { toast.error("Failed to create banner"); }
        finally { setLoading(false); }
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4">
            <div className="bg-white rounded-2xl w-full max-w-md p-6 shadow-xl animate-in fade-in zoom-in duration-200">
                <h3 className="text-lg font-bold mb-4">Add New Banner</h3>
                <form onSubmit={handleSubmit} className="space-y-4">
                    <div>
                        <label className="block text-sm font-bold text-gray-700 mb-2">Title</label>
                        <input type="text" required className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all bg-white" value={title} onChange={e => setTitle(e.target.value)} />
                    </div>
                    <div>
                        <label className="block text-sm font-bold text-gray-700 mb-2">Link URL (Optional)</label>
                        <input type="url" className="w-full px-4 py-2.5 rounded-lg border border-gray-200 focus:border-blue-500 focus:ring-2 focus:ring-blue-100 outline-none transition-all bg-white" value={link} onChange={e => setLink(e.target.value)} placeholder="https://..." />
                    </div>
                    <div>
                        <label className="block text-sm font-bold text-gray-700 mb-2">Image</label>
                        <div className="border-2 border-dashed border-gray-300 rounded-xl aspect-video relative flex items-center justify-center bg-gray-50 overflow-hidden cursor-pointer hover:bg-gray-100 transition-colors">
                            {preview ? (
                                <img src={preview} className="w-full h-full object-cover" />
                            ) : (
                                <div className="text-center p-4">
                                    <Upload className="w-8 h-8 mx-auto text-gray-400 mb-2" />
                                    <span className="text-xs text-gray-500">Click to upload</span>
                                </div>
                            )}
                            <input type="file" required accept="image/*" className="absolute inset-0 opacity-0 cursor-pointer" onChange={handleFile} />
                        </div>
                    </div>
                    <div className="flex gap-3 pt-2">
                        <button type="button" onClick={onClose} className="flex-1 py-2.5 rounded-lg border font-medium hover:bg-gray-50">Cancel</button>
                        <button type="submit" disabled={loading} className="flex-1 py-2.5 bg-blue-600 text-white rounded-lg font-bold hover:bg-blue-700 disabled:opacity-70 flex justify-center items-center gap-2">
                            {loading && <Loader2 className="w-4 h-4 animate-spin" />} Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    );
}

function Upload(props: any) {
    return (
        <svg
            {...props}
            xmlns="http://www.w3.org/2000/svg"
            width="24"
            height="24"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            strokeWidth="2"
            strokeLinecap="round"
            strokeLinejoin="round"
        >
            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4" />
            <polyline points="17 8 12 3 7 8" />
            <line x1="12" x2="12" y1="3" y2="15" />
        </svg>
    )
}
