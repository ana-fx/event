import { Suspense } from "react";
import { Loader2 } from "lucide-react";
import ContactsContent from "./ContactsContent";

// Force dynamic rendering
export const dynamic = "force-dynamic";
export const fetchCache = "force-no-store";

export default function ContactsPage() {
    return (
        <Suspense fallback={<div className="flex center h-screen justify-center items-center"><Loader2 className="animate-spin text-primary w-8 h-8" /></div>}>
            <ContactsContent />
        </Suspense>
    );
}
