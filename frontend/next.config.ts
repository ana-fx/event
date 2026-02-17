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
};

export default nextConfig;
