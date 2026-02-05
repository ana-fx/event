"use client";

import { useState, useRef, useEffect } from "react";
import { ChevronDown, Check } from "lucide-react";
import { cn } from "@/lib/utils";

export interface SelectOption {
    label: string;
    value: string;
}

interface SelectProps {
    label?: string;
    value: string;
    onChange: (value: string) => void;
    options: SelectOption[];
    placeholder?: string;
    error?: string;
    className?: string; // Trigger button class
    containerClassName?: string;
    disabled?: boolean;
}

export function Select({
    label,
    value,
    onChange,
    options,
    placeholder = "Select option",
    error,
    className,
    containerClassName,
    disabled
}: SelectProps) {
    const [isOpen, setIsOpen] = useState(false);
    const containerRef = useRef<HTMLDivElement>(null);

    const selectedOption = options.find((opt) => opt.value === value);

    // Close on click outside
    useEffect(() => {
        const handleClickOutside = (event: MouseEvent) => {
            if (containerRef.current && !containerRef.current.contains(event.target as Node)) {
                setIsOpen(false);
            }
        };

        document.addEventListener("mousedown", handleClickOutside);
        return () => document.removeEventListener("mousedown", handleClickOutside);
    }, []);

    const handleSelect = (optionValue: string) => {
        onChange(optionValue);
        setIsOpen(false);
    };

    return (
        <div className={cn("space-y-2", containerClassName)} ref={containerRef}>
            {label && (
                <label className="block text-sm font-bold text-(--foreground) opacity-70">
                    {label}
                </label>
            )}
            <div className="relative">
                <button
                    type="button"
                    onClick={() => !disabled && setIsOpen(!isOpen)}
                    disabled={disabled}
                    className={cn(
                        "w-full text-left px-4 py-2.5 rounded-lg border border-(--card-border) bg-(--card) text-(--foreground) font-medium transition-all flex items-center justify-between outline-none",
                        "hover:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30",
                        disabled ? "opacity-50 cursor-not-allowed bg-(--background)" : "cursor-pointer",
                        error ? "border-red-500 focus:ring-red-100" : "",
                        className
                    )}
                >
                    <span className={cn("block truncate", !selectedOption && "text-gray-400")}>
                        {selectedOption ? selectedOption.label : placeholder}
                    </span>
                    <ChevronDown className={cn("w-4 h-4 text-gray-400 transition-transform duration-200", isOpen && "rotate-180")} />
                </button>

                {/* Dropdown Menu */}
                <div
                    className={cn(
                        "absolute z-50 w-full mt-2 bg-(--card)/90 backdrop-blur-xl border border-(--card-border) rounded-xl shadow-xl overflow-hidden transition-all duration-200 origin-top",
                        isOpen ? "opacity-100 scale-100 translate-y-0" : "opacity-0 scale-95 -translate-y-2 pointer-events-none"
                    )}
                >
                    <div className="max-h-60 overflow-y-auto py-1 scrollbar-thin scrollbar-thumb-gray-200 scrollbar-track-transparent">
                        {options.length === 0 ? (
                            <div className="px-4 py-3 text-sm text-gray-400 text-center">
                                No options available
                            </div>
                        ) : (
                            options.map((option) => (
                                <div
                                    key={option.value}
                                    onClick={() => handleSelect(option.value)}
                                    className={cn(
                                        "px-4 py-2.5 text-sm font-medium cursor-pointer flex items-center justify-between transition-colors",
                                        option.value === value
                                            ? "bg-blue-600/10 text-blue-600 dark:text-blue-400"
                                            : "text-(--foreground) hover:bg-(--background) opacity-80 hover:opacity-100"
                                    )}
                                >
                                    <span>{option.label}</span>
                                    {option.value === value && <Check className="w-4 h-4" />}
                                </div>
                            ))
                        )}
                    </div>
                </div>
            </div>
            {error && (
                <p className="text-sm text-red-500">{error}</p>
            )}
        </div>
    );
}
