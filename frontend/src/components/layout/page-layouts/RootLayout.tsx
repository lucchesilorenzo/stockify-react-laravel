import { Toaster } from "sonner";
import FontSizeProvider from "@/contexts/FontSizeProvider";
import ThemeProvider from "@/contexts/ThemeProvider";
import { Outlet } from "react-router-dom";

export default function RootLayout() {
  return (
    <>
      <ThemeProvider
        attribute="class"
        defaultTheme="system"
        enableSystem
        disableTransitionOnChange
      >
        <FontSizeProvider>
          <Outlet />
        </FontSizeProvider>
      </ThemeProvider>
      <Toaster position="top-right" duration={4000} visibleToasts={1} />
    </>
  );
}
