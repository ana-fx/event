import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";

export default function CookiePolicyPage() {
    return (
        <div className="min-h-screen bg-white flex flex-col">
            <Navbar />
            <main className="flex-1 w-full max-w-4xl mx-auto px-6 py-24 mt-[80px]">
                <h1 className="text-4xl font-black text-gray-900 mb-8">Cookie Policy</h1>
                <div className="prose prose-lg prose-blue max-w-none text-gray-600">
                    <p>This Cookie Policy explains how Ingate uses cookies and similar technologies to recognize you when you visit our website.</p>

                    <h3>1. What are cookies?</h3>
                    <p>Cookies are small data files that are placed on your computer or mobile device when you visit a website. Cookies are widely used by website owners in order to make their websites work, or to work more efficiently, as well as to provide reporting information.</p>

                    <h3>2. Why do we use cookies?</h3>
                    <p>We use first-party and third-party cookies for several reasons. Some cookies are required for technical reasons in order for our Website to operate, and we refer to these as "essential" or "strictly necessary" cookies. Other cookies also enable us to track and target the interests of our users to enhance the experience on our Online Properties.</p>

                    <h3>3. Types of cookies we use</h3>
                    <ul>
                        <li><strong>Essential website cookies:</strong> These cookies are strictly necessary to provide you with services available through our Website and to use some of its features, such as access to secure areas.</li>
                        <li><strong>Analytics and customization cookies:</strong> These cookies collect information that is used either in aggregate form to help us understand how our Website is being used or how effective our marketing campaigns are, or to help us customize our Website for you.</li>
                    </ul>

                    <h3>4. How can I control cookies?</h3>
                    <p>You have the right to decide whether to accept or reject cookies. You can set or amend your web browser controls to accept or refuse cookies.</p>
                </div>
            </main>
            <Footer />
        </div>
    );
}
