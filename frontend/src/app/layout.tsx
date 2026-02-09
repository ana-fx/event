import type { Metadata } from "next";
import { Plus_Jakarta_Sans, Syne } from "next/font/google";
import "./globals.css";
import ToastProvider from "@/components/providers/ToastProvider";

const plusJakartaSans = Plus_Jakarta_Sans({ 
  subsets: ["latin"],
  variable: '--font-body',
});

const syne = Syne({
  subsets: ["latin"],
  variable: '--font-heading',
});

export const metadata: Metadata = {
  title: "Ingate - Event Ticketing Platform",
  description: "Secure and premium event ticketing platform.",
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className={`${plusJakartaSans.variable} ${syne.variable}`}>
      <body className="antialiased font-body">
        <ToastProvider />
        {children}
      </body>
    </html>
  );
}
