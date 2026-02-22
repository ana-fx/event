"use client";

import { useSearchParams } from "next/navigation";
import OrganizerForm from "../OrganizerForm";

export default function EditOrganizerContent() {
    const searchParams = useSearchParams();
    const id = searchParams.get("id");

    if (!id) return <div className="p-20 text-center text-red-500 font-bold">Error: Resource ID is missing.</div>;

    return <OrganizerForm id={id} />;
}
