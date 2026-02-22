import { Suspense } from "react";
import { Loader2 } from "lucide-react";
import OrganizerReportsContent from "./OrganizerReportsContent";

export const dynamic = "force-dynamic";
export const fetchCache = "force-no-store";

export default function OrganizerReportsPage() {
    return (
        <Suspense fallback={<div className="flex justify-center items-center h-screen"><Loader2 className="animate-spin text-primary w-8 h-8" /></div>}>
            <OrganizerReportsContent />
        </Suspense>
    );
}
