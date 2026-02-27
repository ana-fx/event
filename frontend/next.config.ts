import type { NextConfig } from "next";

const nextConfig = {
  images: {
    unoptimized: true,
    remotePatterns: [
      {
        protocol: 'https',
        hostname: 'images.unsplash.com',
      },
      {
        protocol: 'http',
        hostname: 'localhost',
        port: '8080',
      },
      {
        protocol: 'http',
        hostname: 'localhost',
        port: '8080',
        pathname: '/storage/**',
      },
      {
        protocol: 'https',
        hostname: 'ingate.id',
      },
    ],
  },
  // Optimizations for low-memory servers
  typescript: {
    ignoreBuildErrors: true,
  },
  async rewrites() {
    // BACKEND_URL must be set in Vercel Environment Variables.
    // Fallback to localhost for local development only.
    const backendUrl = process.env.BACKEND_URL || 'http://localhost:8080';
    console.log(`[next.config] Using BACKEND_URL: ${backendUrl}`);
    return [
      {
        source: '/api/:path*',
        destination: `${backendUrl}/api/:path*`,
      },
      {
        source: '/uploads/:path*',
        destination: `${backendUrl}/uploads/:path*`,
      },
    ];
  },
};

export default nextConfig;
