import Navbar from "@/components/public/Navbar";
import Footer from "@/components/public/Footer";

export default function PrivacyPage() {
    return (
        <div className="min-h-screen bg-white flex flex-col selection:bg-primary/5 selection:text-primary">
            <Navbar />

            <main className="flex-1 w-full bg-white relative overflow-hidden pt-48 pb-24">
                {/* Decorative Background Elements */}
                <div className="absolute top-0 right-0 w-1/3 h-1/3 bg-primary/5 rounded-full blur-[120px] -translate-y-1/2 translate-x-1/2"></div>
                <div className="absolute bottom-0 left-0 w-1/4 h-1/4 bg-brand-dark/5 rounded-full blur-[100px] translate-y-1/4 -translate-x-1/4"></div>

                <div className="relative z-10 max-w-5xl mx-auto px-6 lg:px-10">
                    <header className="mb-24">
                        <span className="text-primary text-[10px] font-black uppercase tracking-[0.6em] mb-6 block animate-in slide-in-from-bottom duration-700">Legal Documents</span>
                        <h1 className="text-6xl md:text-8xl lg:text-9xl font-black text-brand-dark tracking-tighter uppercase leading-[0.8] font-heading animate-in slide-in-from-bottom duration-1000 delay-100">
                            Privacy <br />
                            <span className="text-gray-200">Policy</span>
                            <span className="text-primary">.</span>
                        </h1>
                    </header>

                    <div className="grid grid-cols-1 lg:grid-cols-12 gap-16">
                        <aside className="lg:col-span-4 lg:sticky lg:top-40 h-fit space-y-6 hidden lg:block border-l-2 border-gray-50 pl-8">
                            <p className="text-[10px] font-black uppercase tracking-widest text-gray-400 mb-8 font-heading">Last Updated: March 2024</p>
                            <nav className="space-y-4">
                                {[
                                    { name: 'Overview', id: 'overview' },
                                    { name: 'Information We Collect', id: 'collect' },
                                    { name: 'How We Use It', id: 'use' },
                                    { name: 'Data Protection', id: 'protection' },
                                    { name: 'Third Parties', id: 'third-party' }
                                ].map((item) => (
                                    <a key={item.id} href={`#${item.id}`} className="block text-sm font-bold text-gray-400 hover:text-brand-dark transition-colors capitalize">
                                        {item.name}
                                    </a>
                                ))}
                            </nav>
                        </aside>

                        <article className="lg:col-span-8 space-y-20">
                            <section id="overview" className="prose prose-2xl prose-gray max-w-none scroll-mt-32">
                                <p className="text-xl text-gray-500 font-medium leading-relaxed font-body italic border-b border-gray-100 pb-12">
                                    Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your personal information.
                                </p>
                            </section>

                            <section id="collect" className="space-y-8 scroll-mt-32">
                                <div className="flex items-baseline gap-4">
                                    <span className="text-4xl font-black text-primary/70 font-heading">01</span>
                                    <h2 className="text-3xl font-black text-brand-dark uppercase tracking-tight font-heading">Information We Collect</h2>
                                </div>
                                <p className="text-lg text-gray-600 font-medium leading-relaxed font-body">
                                    We may collect personal information such as your name, email address, and phone number when you create an account or purchase tickets.
                                </p>
                            </section>

                            <section id="use" className="space-y-8 scroll-mt-32">
                                <div className="flex items-baseline gap-4">
                                    <span className="text-4xl font-black text-primary/70 font-heading">02</span>
                                    <h2 className="text-3xl font-black text-brand-dark uppercase tracking-tight font-heading">How We Use Your Information</h2>
                                </div>
                                <p className="text-lg text-gray-600 font-medium leading-relaxed font-body">
                                    We use your information to process transactions, send event updates, and improve our services.
                                </p>
                            </section>

                            <section id="protection" className="space-y-8 scroll-mt-32">
                                <div className="flex items-baseline gap-4">
                                    <span className="text-4xl font-black text-primary/70 font-heading">03</span>
                                    <h2 className="text-3xl font-black text-brand-dark uppercase tracking-tight font-heading">Data Protection</h2>
                                </div>
                                <p className="text-lg text-gray-600 font-medium leading-relaxed font-body">
                                    We implement security measures to maintain the safety of your personal information.
                                </p>
                            </section>

                            <section id="third-party" className="space-y-8 scroll-mt-32">
                                <div className="flex items-baseline gap-4">
                                    <span className="text-4xl font-black text-primary/70 font-heading">04</span>
                                    <h2 className="text-3xl font-black text-brand-dark uppercase tracking-tight font-heading">Third-Party Disclosures</h2>
                                </div>
                                <p className="text-lg text-gray-600 font-medium leading-relaxed font-body">
                                    We do not sell, trade, or otherwise transfer your personally identifiable information to outside parties unless we provide users with advance notice.
                                </p>
                            </section>
                        </article>
                    </div>
                </div>
            </main>
            <Footer />
        </div>
    );
}
