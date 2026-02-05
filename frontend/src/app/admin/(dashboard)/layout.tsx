import Sidebar from "@/components/admin/Sidebar";
import Header from "@/components/admin/Header";
import { SidebarProvider } from "@/context/SidebarContext";
import { ThemeProvider } from "@/context/ThemeContext";

export default function AdminLayout({
    children,
}: {
    children: React.ReactNode;
}) {
    return (
        <ThemeProvider>
            <SidebarProvider>
                <div className="flex min-h-screen bg-(--background) text-(--foreground)">
                    <Sidebar />
                    <Header />
                    <main className="flex-1 md:ml-72 pt-20 min-h-screen bg-(--background)">
                        <div className="p-4 md:p-8 w-full">
                            {children}
                        </div>
                    </main>
                </div>
            </SidebarProvider>
        </ThemeProvider>
    );
}
