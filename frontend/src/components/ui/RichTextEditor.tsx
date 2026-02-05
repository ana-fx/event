"use client";

import dynamic from 'next/dynamic';
import { useMemo } from 'react';
import 'react-quill-new/dist/quill.snow.css';

interface RichTextEditorProps {
    value: string;
    onChange: (value: string) => void;
    label?: string;
    placeholder?: string;
}

export default function RichTextEditor({ value, onChange, label, placeholder }: RichTextEditorProps) {
    // Dynamic import to avoid SSR issues
    const ReactQuill = useMemo(() => dynamic(() => import('react-quill-new'), {
        ssr: false,
        loading: () => <div className="h-48 w-full bg-gray-50 animate-pulse rounded-lg border border-gray-100" />
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
    }), []) as any;

    return (
        <div className="space-y-2">
            {label && <label className="text-sm font-bold text-(--foreground)">{label}</label>}
            <div className="bg-(--card) text-(--foreground) border border-(--card-border) rounded-xl overflow-hidden focus-within:ring-4 focus-within:ring-blue-500/10 focus-within:border-blue-500 transition-all
                [&_.ql-editor]:min-h-[150px] [&_.ql-editor]:text-base
                [&_.ql-snow_.ql-stroke]:stroke-current [&_.ql-snow_.ql-fill]:fill-current [&_.ql-snow_.ql-picker]:text-current
                [&_.ql-snow.ql-toolbar_button:hover]:text-blue-500 [&_.ql-snow.ql-toolbar_button:hover_.ql-stroke]:stroke-blue-500
                dark:[&_.ql-toolbar]:bg-gray-800/50">
                <ReactQuill
                    theme="snow"
                    value={value || ''}
                    onChange={onChange}
                    placeholder={placeholder}
                    className="h-64"
                />
            </div>
        </div>
    );
}
