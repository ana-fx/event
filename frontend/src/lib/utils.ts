import { type ClassValue, clsx } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
    return twMerge(clsx(inputs));
}

export function getImageUrl(path: string | null | undefined): string {
    if (!path) return "";
    if (path.startsWith("http") || path.startsWith("data:")) return path;

    const apiUrl = process.env.NEXT_PUBLIC_API_URL || "/api";
    // If apiUrl is just "/api", baseUrl should be empty string to allow relative path "/uploads/..."
    // If apiUrl is "http://localhost:8080/api", baseUrl is "http://localhost:8080"
    const baseUrl = apiUrl.endsWith("/api") ? apiUrl.slice(0, -4) : apiUrl;

    // Ensure path starts with / if it doesn't
    const cleanPath = path.startsWith("/") ? path : `/${path}`;

    return `${baseUrl}${cleanPath}`;
}
