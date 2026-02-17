import { Suspense } from "react";
import { Loader2 } from "lucide-react";
import EditEventContent from "./EditEventContent";

// Force dynamic rendering to skip static generation and avoid build errors
export const dynamic = "force-dynamic";
export const fetchCache = "force-no-store";

export default function EditEvent() {
    return (
        <Suspense fallback={<div className="flex items-center justify-center min-h-screen"><Loader2 className="w-8 h-8 animate-spin text-blue-600" /></div>}>
            <EditEventContent />
        </Suspense>
    );
}
