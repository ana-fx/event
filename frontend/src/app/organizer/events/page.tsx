import { Suspense } from "react";
import { Loader2 } from "lucide-react";
import OrganizerEventListContent from "./OrganizerEventListContent";

export const dynamic = "force-dynamic";
export const fetchCache = "force-no-store";

export default function OrganizerEventsPage() {
    return (
        <Suspense fallback={<div className="flex justify-center items-center h-screen"><Loader2 className="animate-spin text-primary w-8 h-8" /></div>}>
            <OrganizerEventListContent />
        </Suspense>
    );
}
