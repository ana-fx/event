"use client";

import { cn } from "@/lib/utils";

interface TabItem {
    key: string;
    label: string;
}

interface TabsProps {
    items: TabItem[];
    activeKey: string;
    onChange: (key: string) => void;
    className?: string;
}

export default function Tabs({ items, activeKey, onChange, className }: TabsProps) {
    return (
        <div className={cn("border-b border-gray-200", className)}>
            <div className="flex gap-6">
                {items.map((item) => (
                    <button
                        key={item.key}
                        onClick={() => onChange(item.key)}
                        className={cn(
                            "pb-4 text-sm font-medium border-b-2 transition-colors",
                            activeKey === item.key
                                ? "border-blue-600 text-blue-600"
                                : "border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                        )}
                    >
                        {item.label}
                    </button>
                ))}
            </div>
        </div>
    );
}
