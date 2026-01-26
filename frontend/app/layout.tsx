// frontend/app/layout.tsx
import type { Metadata, Viewport } from "next";
import { Inter } from "next/font/google";
import "./globals.css";

const inter = Inter({
  subsets: ["latin"],
  weight: ["400", "500", "600", "700"],
  variable: "--font-inter",
});

export const viewport: Viewport = {
  width: "device-width",
  initialScale: 1,
  maximumScale: 5,
  userScalable: true,
};

export const metadata: Metadata = {
  metadataBase: new URL(process.env.NEXT_PUBLIC_APP_URL || "https://todokizamu.me"),
  title: "ToDoKizamu - Task Management",
  description: "Modern task and learning management platform",
  icons: {
    icon: "/logo/logo.png",
    shortcut: "/logo/logo.png",
    apple: "/logo/logo.png",
  },
  openGraph: {
    title: "ToDoKizamu - Task Management",
    description: "Modern task and learning management platform",
    url: "/",
    siteName: "ToDoKizamu",
    images: [{ url: "/logo/logo.png", width: 512, height: 512, alt: "ToDoKizamu" }],
    type: "website",
  },
  twitter: {
    card: "summary_large_image",
    title: "ToDoKizamu - Task Management",
    description: "Modern task and learning management platform",
    images: ["/logo/logo.png"],
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" suppressHydrationWarning>
      <body className={`${inter.className} antialiased`} suppressHydrationWarning>
        {children}
      </body>
    </html>
  );
}