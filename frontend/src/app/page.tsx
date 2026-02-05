import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";
import Hero from "@/components/public/Hero";
import EventList from "@/components/public/EventList";

export default function Home() {
  return (
    <div className="min-h-screen flex flex-col bg-gray-50">
      <Navbar />

      <main className="flex-1 pt-16">
        <Hero />

        <section className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          <div className="flex justify-between items-end mb-8">
            <div>
              <h2 className="text-3xl font-bold text-gray-900 tracking-tight">Upcoming Events</h2>
              <p className="text-gray-500 mt-2">Discover the latest concerts, workshops, and parties.</p>
            </div>
            <a href="/events" className="text-blue-600 font-semibold hover:underline hidden sm:block">View All Events &rarr;</a>
          </div>

          <EventList />

          <div className="mt-8 text-center sm:hidden">
            <a href="/events" className="text-blue-600 font-semibold hover:underline">View All Events &rarr;</a>
          </div>
        </section>
      </main>

      <Footer />
    </div>
  );
}
