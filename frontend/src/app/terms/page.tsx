import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";

export default function TermsPage() {
    return (
        <div className="min-h-screen bg-white flex flex-col">
            <Navbar />
            <main className="flex-1 w-full max-w-4xl mx-auto px-6 py-24">
                <h1 className="text-4xl font-black text-gray-900 mb-8">Terms of Service</h1>
                <div className="prose prose-lg prose-blue max-w-none text-gray-600">
                    <p>Welcome to Ingate. By using our website and services, you agree to the following terms and conditions.</p>

                    <h3>1. Acceptance of Terms</h3>
                    <p>By accessing or using our platform, you agree to be bound by these Terms of Service and our Privacy Policy.</p>

                    <h3>2. User Responsibilities</h3>
                    <p>You are responsible for maintaining the confidentiality of your account information and for all activities that occur under your account.</p>

                    <h3>3. Ticket Purchases</h3>
                    <p>All ticket purchases are final. Refunds are only issued in accordance with our Refund Policy or if an event is cancelled.</p>

                    <h3>4. Code of Conduct</h3>
                    <p>You agree not to use the platform for any unlawful purpose or to solicit others to perform or participate in any unlawful acts.</p>

                    <h3>5. Limitation of Liability</h3>
                    <p>Ingate shall not be liable for any indirect, incidental, special, or consequential damages resulting from your use of the service.</p>
                </div>
            </main>
            <Footer />
        </div>
    );
}
