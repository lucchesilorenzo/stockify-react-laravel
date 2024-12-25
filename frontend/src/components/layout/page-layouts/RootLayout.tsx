import { Toaster } from "sonner";
import FontSizeProvider from "@/contexts/FontSizeProvider";
import { Outlet } from "react-router-dom";
import ThemeProvider from "@/contexts/ThemeProvider";

export default function RootLayout() {
  return (
    <>
      <ThemeProvider defaultTheme="system" storageKey="vite-ui-theme">
        <FontSizeProvider>
          <Outlet />
        </FontSizeProvider>
      </ThemeProvider>
      <Toaster position="top-right" duration={4000} visibleToasts={1} />
    </>
  );
}
