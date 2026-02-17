import { Suspense } from "react";
import { Loader2 } from "lucide-react";
import BannersContent from "./BannersContent";

// Force dynamic rendering to skip static generation and avoid build errors
export const dynamic = "force-dynamic";
export const fetchCache = "force-no-store";

export default function BannersPage() {
    return (
        <Suspense fallback={<div className="flex center h-screen justify-center items-center"><Loader2 className="animate-spin text-blue-600 w-8 h-8" /></div>}>
            <BannersContent />
        </Suspense>
    );
}
