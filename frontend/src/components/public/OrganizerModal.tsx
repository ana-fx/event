"use client";

import { useState } from "react";
import { X, MapPin, Phone, Globe, Building2 } from "lucide-react";
import { getImageUrl } from "@/lib/utils";
import Image from "next/image";

interface OrganizerProfile {
    id: number;
    name: string;
    organizer_name: { String: string; Valid: boolean };
    about_us: { String: string; Valid: boolean };
    phone: { String: string; Valid: boolean };
    province: { String: string; Valid: boolean };
    city: { String: string; Valid: boolean };
    profile_photo_path: { String: string; Valid: boolean };
}

interface Props {
    organizerId: number;
    organizerName: string;
    organizerLogo: string | null;
}

export default function OrganizerModal({ organizerId, organizerName, organizerLogo }: Props) {
    const [open, setOpen] = useState(false);
    const [profile, setProfile] = useState<OrganizerProfile | null>(null);
    const [loading, setLoading] = useState(false);

    const handleOpen = async () => {
        setOpen(true);
        if (profile) return;
        setLoading(true);
        try {
            const res = await fetch(`/api/organizers/profile?id=${organizerId}`);
            if (res.ok) setProfile(await res.json());
        } catch { /* ignore */ }
        finally { setLoading(false); }
    };

    const displayName = profile?.organizer_name?.Valid
        ? profile.organizer_name.String
        : organizerName;

    const location = [
        profile?.city?.Valid && profile.city.String,
        profile?.province?.Valid && profile.province.String,
    ].filter(Boolean).join(", ");

    return (
        <>
            {/* Trigger — wrap seluruh card organizer */}
            <button
                onClick={handleOpen}
                className="w-full text-left group"
            >
                <div className="p-10 rounded-[40px] bg-gray-50 border border-gray-100 hover:border-primary/20 hover:bg-primary/5 transition-all duration-300 flex flex-col md:flex-row gap-8 items-center">
                    <div className="w-24 h-24 rounded-[28px] bg-white p-2 shadow-xl relative overflow-hidden flex items-center justify-center shrink-0">
                        {organizerLogo ? (
                            <Image
                                src={getImageUrl(organizerLogo)}
                                alt={organizerName}
                                fill
                                className="object-cover p-2"
                            />
                        ) : (
                            <Globe className="w-10 h-10 text-gray-300" />
                        )}
                    </div>
                    <div className="flex-1 text-center md:text-left">
                        <span className="text-[10px] font-black uppercase tracking-[0.3em] text-primary mb-2 block">Our Trusted Partner</span>
                        <h3 className="text-3xl font-black text-brand-dark tracking-tight uppercase mb-2 group-hover:text-primary transition-colors">
                            {organizerName || "Official Partner"}
                        </h3>
                        <p className="text-gray-500 max-w-md text-sm">Experience events crafted with excellence and brought to you by industry legends.</p>
                        <span className="inline-block mt-3 text-[10px] font-black uppercase tracking-widest text-primary/70 group-hover:text-primary transition-colors">
                            Lihat Profil →
                        </span>
                    </div>
                </div>
            </button>

            {/* Modal */}
            {open && (
                <div
                    className="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
                    onClick={() => setOpen(false)}
                >
                    <div
                        className="bg-white rounded-[32px] w-full max-w-md shadow-2xl overflow-hidden"
                        onClick={e => e.stopPropagation()}
                    >
                        {/* Header bar */}
                        <div className="bg-brand-dark px-6 pt-8 pb-10 relative">
                            <button
                                onClick={() => setOpen(false)}
                                className="absolute top-4 right-4 w-8 h-8 rounded-full bg-white/10 hover:bg-white/20 flex items-center justify-center transition-colors"
                            >
                                <X className="w-4 h-4 text-white" />
                            </button>
                            <span className="text-[9px] font-black uppercase tracking-[0.3em] text-primary block mb-2">Organizer Profile</span>
                            <h2 className="text-2xl font-black text-white uppercase tracking-tight leading-tight">
                                {displayName || organizerName}
                            </h2>
                        </div>

                        {/* Avatar overlap */}
                        <div className="flex justify-center -mt-8">
                            <div className="w-16 h-16 rounded-2xl bg-white shadow-xl ring-4 ring-white relative overflow-hidden flex items-center justify-center">
                                {organizerLogo ? (
                                    <Image src={getImageUrl(organizerLogo)} alt={organizerName} fill className="object-cover p-1" />
                                ) : (
                                    <Building2 className="w-7 h-7 text-gray-300" />
                                )}
                            </div>
                        </div>

                        {/* Body */}
                        <div className="px-6 py-5 space-y-4">
                            {loading && (
                                <div className="flex justify-center py-6">
                                    <div className="w-6 h-6 border-2 border-primary border-t-transparent rounded-full animate-spin" />
                                </div>
                            )}

                            {!loading && profile && (
                                <>
                                    {profile.about_us?.Valid && (
                                        <div>
                                            <p className="text-[9px] font-black uppercase tracking-widest text-gray-400 mb-1.5">Tentang</p>
                                            <p className="text-sm text-gray-600 leading-relaxed">{profile.about_us.String}</p>
                                        </div>
                                    )}

                                    <div className="space-y-2.5 pt-2 border-t border-gray-100">
                                        {location && (
                                            <div className="flex items-center gap-3 text-sm text-gray-600">
                                                <div className="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center shrink-0">
                                                    <MapPin className="w-4 h-4 text-gray-400" />
                                                </div>
                                                {location}
                                            </div>
                                        )}
                                        {profile.phone?.Valid && (
                                            <div className="flex items-center gap-3 text-sm text-gray-600">
                                                <div className="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center shrink-0">
                                                    <Phone className="w-4 h-4 text-gray-400" />
                                                </div>
                                                {profile.phone.String}
                                            </div>
                                        )}
                                    </div>
                                </>
                            )}

                            {!loading && !profile && (
                                <p className="text-center text-sm text-gray-400 py-4">Profil tidak tersedia</p>
                            )}
                        </div>

                        <div className="px-6 pb-6">
                            <button
                                onClick={() => setOpen(false)}
                                className="w-full py-3 rounded-2xl bg-brand-dark text-white text-sm font-black uppercase tracking-widest hover:bg-primary transition-colors"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </>
    );
}
