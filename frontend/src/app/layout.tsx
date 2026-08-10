import type { Metadata } from "next";
import { Plus_Jakarta_Sans, Syne } from "next/font/google";
import "./globals.css";
import ToastProvider from "@/components/providers/ToastProvider";
import { SpeedInsights } from '@vercel/speed-insights/next';

const plusJakartaSans = Plus_Jakarta_Sans({
  subsets: ["latin"],
  variable: '--font-body',
});

const syne = Syne({
  subsets: ["latin"],
  variable: '--font-heading',
});

export const metadata: Metadata = {
  metadataBase: new URL('https://ingate.id'),
  title: {
    default: "Ingate - Premier Event Ticketing Platform",
    template: "%s | Ingate",
  },
  description: "Discover and book tickets for the best concerts, festivals, and exclusive events in Indonesia. Secure, fast, and premium ticketing experience.",
  keywords: ["tiket konser", "event jakarta", "konser musik", "festival indonesia", "beli tiket online", "ingate", "ticketing platform", "music festival jakarta", "konser 2026"],
  authors: [{ name: "Ingate Team" }],
  creator: "Ingate",
  publisher: "Ingate",
  alternates: {
    canonical: '/',
  },
  openGraph: {
    type: "website",
    locale: "id_ID",
    url: "https://ingate.id",
    title: "Ingate - Premier Event Ticketing Platform",
    description: "Discover and book tickets for the best concerts, festivals, and exclusive events in Indonesia.",
    siteName: "Ingate",
    images: [
      {
        url: "/og-image.jpg",
        width: 1200,
        height: 630,
        alt: "Ingate Event Platform",
      },
    ],
  },
  twitter: {
    card: "summary_large_image",
    title: "Ingate - Premier Event Ticketing Platform",
    description: "Discover and book tickets for the best concerts, festivals, and exclusive events in Indonesia.",
    creator: "@ingate_id",
    images: ["/og-image.jpg"],
  },
  icons: {
    icon: "/favicon.ico",
  },
  robots: {
    index: true,
    follow: true,
  },
  verification: {
    google: 'OdWQUHFp8X3aIb5vm2iIfL5EJRmZ8XdTs-mxe77UhFY',
  }
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
        <SpeedInsights />
      </body>
    </html>
  );
}
