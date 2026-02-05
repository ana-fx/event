"use client";

import { useState, useRef, useEffect } from "react";
import { format, addMonths, subMonths, startOfMonth, endOfMonth, startOfWeek, endOfWeek, eachDayOfInterval, isSameMonth, isSameDay, setHours, setMinutes } from "date-fns";
import { Calendar as CalendarIcon, ChevronLeft, ChevronRight, Clock } from "lucide-react";
import { cn } from "@/lib/utils";

interface DatePickerProps {
    label?: string;
    value?: string; // ISO string
    onChange: (value: string) => void;
    error?: string;
    containerClassName?: string;
}

export function DatePicker({
    label,
    value,
    onChange,
    error,
    containerClassName
}: DatePickerProps) {
    const [isOpen, setIsOpen] = useState(false);
    const containerRef = useRef<HTMLDivElement>(null);

    // Parse initial date or default to now
    const [selectedDate, setSelectedDate] = useState<Date>(value ? new Date(value) : new Date());
    const [viewDate, setViewDate] = useState<Date>(selectedDate); // For navigating months without changing selection

    // Sync internal state if prop changes externally
    useEffect(() => {
        if (value) {
            const d = new Date(value);
            if (!isNaN(d.getTime())) {
                setSelectedDate(d);
                // Only update viewDate if menu is closed to avoid jumping while navigating
                if (!isOpen) setViewDate(d);
            }
        }
    }, [value, isOpen]);

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

    const toggleOpen = () => setIsOpen(!isOpen);

    const handleDayClick = (day: Date) => {
        // Keep the time from the currently selected date
        const newDate = new Date(day);
        newDate.setHours(selectedDate.getHours());
        newDate.setMinutes(selectedDate.getMinutes());

        setSelectedDate(newDate);
        onChange(newDate.toISOString()); // Update parent immediately
        // Don't close immediately so user can set time if needed
    };

    const handleTimeChange = (type: 'hours' | 'minutes', val: string) => {
        const num = parseInt(val, 10);
        if (isNaN(num)) return;

        const newDate = new Date(selectedDate);
        if (type === 'hours') newDate.setHours(Math.min(23, Math.max(0, num)));
        if (type === 'minutes') newDate.setMinutes(Math.min(59, Math.max(0, num)));

        setSelectedDate(newDate);
        onChange(newDate.toISOString());
    };

    const nextMonth = () => setViewDate(addMonths(viewDate, 1));
    const prevMonth = () => setViewDate(subMonths(viewDate, 1));

    // Calendar Generation
    const monthStart = startOfMonth(viewDate);
    const monthEnd = endOfMonth(monthStart);
    const startDate = startOfWeek(monthStart);
    const endDate = endOfWeek(monthEnd);

    const calendarDays = eachDayOfInterval({ start: startDate, end: endDate });

    const weekDays = ["Su", "Mo", "Tu", "We", "Th", "Fr", "Sa"];

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
                    onClick={toggleOpen}
                    className={cn(
                        "w-full text-left px-4 py-2.5 rounded-lg border border-(--card-border) bg-(--card) text-(--foreground) font-medium transition-all flex items-center gap-2 outline-none",
                        "hover:border-blue-400 focus:ring-2 focus:ring-blue-100 dark:focus:ring-blue-900/30",
                        error ? "border-red-500 focus:ring-red-100" : ""
                    )}
                >
                    <CalendarIcon className="w-5 h-5 text-gray-400" />
                    <span className="flex-1">
                        {value ? format(new Date(value), "PPP p") : "Select date..."}
                    </span>
                </button>

                {/* Popover */}
                {isOpen && (
                    <div className="absolute z-50 mt-2 p-4 bg-(--card) rounded-xl shadow-xl border border-(--card-border) w-[300px] animate-in fade-in zoom-in-95 duration-200 backdrop-blur-xl">
                        {/* Header */}
                        <div className="flex justify-between items-center mb-4">
                            <button onClick={prevMonth} type="button" className="p-1 hover:bg-(--background) rounded-lg"><ChevronLeft className="w-5 h-5 text-gray-500" /></button>
                            <span className="font-bold text-(--foreground)">{format(viewDate, "MMMM yyyy")}</span>
                            <button onClick={nextMonth} type="button" className="p-1 hover:bg-(--background) rounded-lg"><ChevronRight className="w-5 h-5 text-gray-500" /></button>
                        </div>

                        {/* Days Header */}
                        <div className="grid grid-cols-7 mb-2 text-center">
                            {weekDays.map(d => <span key={d} className="text-xs font-bold text-gray-400">{d}</span>)}
                        </div>

                        {/* Calendar Grid */}
                        <div className="grid grid-cols-7 gap-1 mb-4">
                            {calendarDays.map((day, i) => {
                                const isSelected = isSameDay(day, selectedDate);
                                const isCurrentMonth = isSameMonth(day, viewDate);
                                return (
                                    <button
                                        key={day.toISOString()}
                                        type="button"
                                        onClick={() => handleDayClick(day)}
                                        className={cn(
                                            "h-9 w-9 rounded-lg flex items-center justify-center text-sm transition-colors",
                                            !isCurrentMonth && "text-gray-300 dark:text-gray-600",
                                            isSelected ? "bg-blue-600 text-white font-bold shadow-md shadow-blue-200 dark:shadow-blue-900/20" : "hover:bg-blue-600/10 text-(--foreground)",
                                            !isSelected && isCurrentMonth && "font-medium"
                                        )}
                                    >
                                        {format(day, "d")}
                                    </button>
                                );
                            })}
                        </div>

                        {/* Time Picker */}
                        <div className="border-t border-(--card-border) pt-4 flex items-center justify-between">
                            <div className="flex items-center gap-2 text-sm text-(--foreground) opacity-60 font-medium">
                                <Clock className="w-4 h-4" /> Time
                            </div>
                            <div className="flex items-center gap-1">
                                <input
                                    type="number"
                                    min="0" max="23"
                                    className="w-12 p-1 text-center border border-(--card-border) bg-(--background) rounded-md focus:border-blue-500 outline-none text-sm font-bold text-(--foreground)"
                                    value={format(selectedDate, "HH")}
                                    onChange={(e) => handleTimeChange('hours', e.target.value)}
                                />
                                <span className="text-gray-400 font-bold">:</span>
                                <input
                                    type="number"
                                    min="0" max="59"
                                    className="w-12 p-1 text-center border border-(--card-border) bg-(--background) rounded-md focus:border-blue-500 outline-none text-sm font-bold text-(--foreground)"
                                    value={format(selectedDate, "mm")}
                                    onChange={(e) => handleTimeChange('minutes', e.target.value)}
                                />
                            </div>
                        </div>
                    </div>
                )}
            </div>
            {error && (
                <p className="text-sm text-red-500">{error}</p>
            )}
        </div>
    );
}
