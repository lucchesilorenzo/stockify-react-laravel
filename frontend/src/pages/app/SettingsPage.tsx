import H1 from "@/components/common/H1";
import Spinner from "@/components/common/spinner";
import SettingsForm from "@/components/settings/SettingsForm";
import { Separator } from "@/components/ui/separator";
import { useSettings } from "@/hooks/queries/useSettings";
import { useEffect } from "react";

export default function SettingsPage() {
  const { data: userSettings, isLoading } = useSettings();

  useEffect(() => {
    document.title = "Settings | Stockify";
  }, []);

  if (isLoading) return <Spinner size="large" />;

  return (
    <main>
      <div className="space-y-1">
        <H1>Settings</H1>
        <p className="text-sm text-muted-foreground">
          Manage your account settings.
        </p>
      </div>

      <Separator className="my-6" />

      {userSettings && <SettingsForm userSettings={userSettings} />}
    </main>
  );
}
