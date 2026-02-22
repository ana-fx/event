import { Suspense } from "react";
import OrganizerListContent from "./OrganizerListContent";

export const dynamic = "force-dynamic";
export const fetchCache = "force-no-store";

export default function OrganizerList() {
    return (
        <Suspense fallback={<div className="p-8 text-center text-gray-400 italic">Initializing organizer list...</div>}>
            <OrganizerListContent />
        </Suspense>
    );
}
