import { useState, useTransition } from "react";

import { CircleUser } from "lucide-react";

import { MainCommandDialog } from "../common/MainCommandDialog";
import FontSizeSwitcher from "./FontSizeSwitcher";
import MobileNavigation from "./MobileNavigation";
import ThemeToggle from "./ThemeToggle";

import { Button } from "@/components/ui/button";
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from "@/components/ui/dropdown-menu";
import { Link, useNavigate } from "react-router-dom";

export default function Header() {
  const [open, setOpen] = useState(false);
  const [isPending, startTransition] = useTransition();
  const navigate = useNavigate();

  function handleLogOut() {
    startTransition(() => {
      localStorage.removeItem("token");
      navigate("/login");
    });
  }

  return (
    <header className="flex h-14 items-center gap-4 border-b bg-muted/40 px-4 lg:h-[60px] lg:px-6">
      <MobileNavigation />
      <div className="w-full flex-1">
        <MainCommandDialog />
      </div>
      <FontSizeSwitcher />
      <ThemeToggle />
      <DropdownMenu open={open} onOpenChange={setOpen}>
        <DropdownMenuTrigger asChild>
          <Button variant="outline" size="icon" className="rounded-full">
            <CircleUser className="h-5 w-5" />
            <span className="sr-only">Toggle user menu</span>
          </Button>
        </DropdownMenuTrigger>
        <DropdownMenuContent align="end">
          <DropdownMenuLabel>My Account</DropdownMenuLabel>
          <DropdownMenuSeparator />
          <DropdownMenuItem asChild>
            <Link to="/app/account/settings" onClick={() => setOpen(!open)}>
              Settings
            </Link>
          </DropdownMenuItem>
          <DropdownMenuSeparator />
          <DropdownMenuItem onClick={handleLogOut}>
            {isPending ? "Logging out..." : "Log out"}
          </DropdownMenuItem>
        </DropdownMenuContent>
      </DropdownMenu>
    </header>
  );
}
