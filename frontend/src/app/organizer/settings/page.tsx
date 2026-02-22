import { Suspense } from "react";
import { Loader2 } from "lucide-react";
import OrganizerSettingsContent from "./OrganizerSettingsContent";

export const dynamic = "force-dynamic";
export const fetchCache = "force-no-store";

export default function OrganizerSettingsPage() {
    return (
        <Suspense fallback={<div className="flex justify-center items-center h-screen"><Loader2 className="animate-spin text-primary w-8 h-8" /></div>}>
            <OrganizerSettingsContent />
        </Suspense>
    );
}
