import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import Hero from "@/components/public/Hero";
import EventList from "@/components/public/EventList";
import CategorySection from "@/components/public/CategorySection";

async function getEvents() {
  try {
    const apiUrl = process.env.NEXT_PUBLIC_API_URL || "http://localhost:8080/api";
    const res = await fetch(`${apiUrl}/events`, {
      cache: "no-store",
    });
    if (!res.ok) return [];
    const data = await res.json();
    return data || [];
  } catch (error) {
    console.error("Failed to fetch events:", error);
    return [];
  }
}

interface Event {
  slug: string;
  name: string;
  description: string;
  start_date: string;
  location: string;
  city: string;
  youtube_link?: string;
}

export default async function Home() {
  const events = await getEvents();

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "ItemList",
    name: "Featured Events | Ingate",
    description: "Temukan tiket konser dan event terbaik di Indonesia.",
    numberOfItems: events.length,
    itemListElement: events.map((event: Event, index: number) => ({
      "@type": "ListItem",
      position: index + 1,
      url: `https://ingate.id/events/${event.slug}`,
      item: {
        "@type": "Event",
        name: event.name,
        description: event.description,
        startDate: event.start_date,
        location: {
          "@type": "Place",
          name: event.location,
          address: {
            "@type": "PostalAddress",
            addressLocality: event.city,
            addressCountry: "ID",
          },
        },
      },
    })),
  };

  return (
    <div className="min-h-screen flex flex-col bg-white">
      <Navbar />

      <main className="flex-1">
        <Hero />
        
        <CategorySection />

        <section className="max-w-7xl mx-auto px-6 lg:px-10 py-24">
          <div className="flex flex-col md:flex-row justify-between items-start md:items-end mb-16 gap-8">
            <div>
              <span className="text-primary text-[10px] font-black uppercase tracking-[0.4em] mb-4 block">
                Discover More
              </span>
              <h2 className="text-5xl md:text-7xl font-black text-brand-dark tracking-tighter uppercase leading-none">
                Upcoming <br /> <span className="text-gray-300">Events</span>
              </h2>
            </div>
            <div className="flex flex-col items-start md:items-end gap-6 max-w-sm">
              <p className="text-gray-400 font-medium text-left md:text-right leading-relaxed">
                Discover the latest concerts, workshops, and exclusive parties
                curated just for you. Get your tickets now before they sold out.
              </p>
              <a
                href="/events"
                className="group flex items-center gap-4 text-[11px] font-black uppercase tracking-[0.3em] text-brand-dark transition-all"
              >
                Explore All
                <div className="w-12 h-12 rounded-full border border-gray-200 flex items-center justify-center group-hover:bg-brand-dark group-hover:text-white group-hover:border-brand-dark transition-all">
                  <span className="text-xl">&rarr;</span>
                </div>
              </a>
            </div>
          </div>

          <EventList initialEvents={events} serverNow={Date.now()} />
        </section>
      </main>

      <Footer />

      {/* Structured Data */}
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(jsonLd) }}
      />
    </div>
  );
}
