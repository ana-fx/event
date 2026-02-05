export default function Footer() {
    return (
        <footer className="bg-gray-900 text-white pt-16 pb-8">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                    <div className="col-span-1 md:col-span-2">
                        <h2 className="text-2xl font-bold mb-4">EventHub</h2>
                        <p className="text-gray-400 max-w-sm">
                            The best platform to discover and book tickets for your favorite concerts, workshops, and exhibitions.
                        </p>
                    </div>
                    <div>
                        <h3 className="font-bold mb-4">Quick Links</h3>
                        <ul className="space-y-2 text-gray-400 text-sm">
                            <li><a href="#" className="hover:text-blue-400 transition-colors">About Us</a></li>
                            <li><a href="#" className="hover:text-blue-400 transition-colors">Explore Events</a></li>
                            <li><a href="#" className="hover:text-blue-400 transition-colors">Contact Support</a></li>
                            <li><a href="#" className="hover:text-blue-400 transition-colors">Terms of Service</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 className="font-bold mb-4">Connect</h3>
                        <ul className="space-y-2 text-gray-400 text-sm">
                            <li><a href="#" className="hover:text-blue-400 transition-colors">Instagram</a></li>
                            <li><a href="#" className="hover:text-blue-400 transition-colors">Twitter</a></li>
                            <li><a href="#" className="hover:text-blue-400 transition-colors">Facebook</a></li>
                            <li><a href="#" className="hover:text-blue-400 transition-colors">LinkedIn</a></li>
                        </ul>
                    </div>
                </div>

                <div className="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                    <p className="text-gray-500 text-sm">© {new Date().getFullYear()} EventHub. All rights reserved.</p>
                    <p className="text-gray-600 text-xs">Built with ❤️ by Artisan IT</p>
                </div>
            </div>
        </footer>
    );
}
