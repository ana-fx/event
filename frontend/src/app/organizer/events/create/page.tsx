import { Suspense } from "react";
import { Loader2 } from "lucide-react";
import CreateEventForm from "@/app/admin/(dashboard)/events/create/CreateEventForm";

export default function OrganizerCreateEventPage() {
    return (
        <Suspense fallback={<div className="flex justify-center items-center h-screen"><Loader2 className="animate-spin text-primary w-8 h-8" /></div>}>
            <div className="p-4 md:p-8">
                <CreateEventForm isOrganizer={true} />
            </div>
        </Suspense>
    );
}
