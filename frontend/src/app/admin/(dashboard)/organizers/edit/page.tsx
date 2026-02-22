import { Suspense } from "react";
import EditOrganizerContent from "./EditOrganizerContent";

export default function EditOrganizer() {
    return (
        <Suspense fallback={<div className="p-20 text-center text-gray-400 italic">Initializing editor...</div>}>
            <EditOrganizerContent />
        </Suspense>
    );
}
