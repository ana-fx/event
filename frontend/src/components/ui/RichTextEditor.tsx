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
        <div className="flex flex-col gap-2">
            {label && <label className="block text-sm font-bold text-gray-700">{label}</label>}
            <div className="bg-white rounded-lg overflow-hidden border border-gray-200 [&_.ql-toolbar]:border-0 [&_.ql-toolbar]:border-b [&_.ql-toolbar]:border-gray-100 [&_.ql-container]:border-0 [&_.ql-editor]:min-h-[150px] [&_.ql-editor]:text-base">
                <ReactQuill
                    theme="snow"
                    value={value || ''}
                    onChange={onChange}
                    placeholder={placeholder}
                    modules={{
                        toolbar: [
                            [{ 'header': [1, 2, 3, false] }],
                            ['bold', 'italic', 'underline', 'strike'],
                            [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                            ['link', 'clean']
                        ],
                    }}
                />
            </div>
        </div>
    );
}
