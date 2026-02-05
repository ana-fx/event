import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import Hero from "@/components/public/Hero";
import EventList from "@/components/public/EventList";

async function getEvents() {
  try {
    const res = await fetch("http://localhost:8080/api/events", { next: { revalidate: 60 } });
    if (!res.ok) return [];
    return res.json();
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
}

export default async function Home() {
  const events = await getEvents();

  const jsonLd = {
    "@context": "https://schema.org",
    "@type": "ItemList",
    "name": "Featured Events | Anntix",
    "description": "Temukan tiket konser dan event terbaik di Indonesia.",
    "numberOfItems": events.length,
    "itemListElement": events.map((event: Event, index: number) => ({
      "@type": "ListItem",
      "position": index + 1,
      "url": `https://anntix.id/events/${event.slug}`,
      "item": {
        "@type": "Event",
        "name": event.name,
        "description": event.description,
        "startDate": event.start_date,
        "location": {
          "@type": "Place",
          "name": event.location,
          "address": {
            "@type": "PostalAddress",
            "addressLocality": event.city,
            "addressCountry": "ID"
          }
        }
      }
    }))
  };

  return (
    <div className="min-h-screen flex flex-col bg-white">
      <Navbar />

      <main className="flex-1 pt-20">
        <Hero />

        <section className="max-w-7xl mx-auto px-6 lg:px-10 py-20">
          <div className="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4">
            <div>
              <h2 className="text-4xl font-black text-gray-900 tracking-tighter uppercase mb-2">
                Upcoming <span className="text-blue-600">Events</span>
              </h2>
              <p className="text-gray-400 font-medium max-w-lg">
                Discover the latest concerts, workshops, and exclusive parties curated just for you.
              </p>
            </div>
            <a href="/events" className="text-[11px] font-black uppercase tracking-[0.3em] text-blue-600 hover:text-gray-900 transition-all border-b-2 border-blue-600 pb-1">
              View All Events &rarr;
            </a>
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

