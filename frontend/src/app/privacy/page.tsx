import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";

export default function PrivacyPage() {
    return (
        <div className="min-h-screen bg-white flex flex-col">
            <Navbar />
            <main className="flex-1 w-full max-w-4xl mx-auto px-6 py-24">
                <h1 className="text-4xl font-black text-gray-900 mb-8">Privacy Policy</h1>
                <div className="prose prose-lg prose-blue max-w-none text-gray-600">
                    <p>Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your personal information.</p>

                    <h3>1. Information We Collect</h3>
                    <p>We may collect personal information such as your name, email address, and phone number when you create an account or purchase tickets.</p>

                    <h3>2. How We Use Your Information</h3>
                    <p>We use your information to process transactions, send event updates, and improve our services.</p>

                    <h3>3. Data Protection</h3>
                    <p>We implement security measures to maintain the safety of your personal information.</p>

                    <h3>4. Third-Party Disclosures</h3>
                    <p>We do not sell, trade, or otherwise transfer your personally identifiable information to outside parties unless we provide users with advance notice.</p>
                </div>
            </main>
            <Footer />
        </div>
    );
}
