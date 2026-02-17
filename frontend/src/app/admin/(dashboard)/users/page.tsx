import { Suspense } from "react";
import UserListContent from "./UserListContent";

// Force dynamic rendering to skip static generation
export const dynamic = "force-dynamic";
export const fetchCache = "force-no-store";

export default function UserList() {
    return (
        <Suspense fallback={<div className="p-8 text-center text-gray-400 italic">Initializing user list...</div>}>
            <UserListContent />
        </Suspense>
    );
}
