import H1 from "@/components/common/H1";
import Spinner from "@/components/common/Spinner";
import DashboardSummary from "@/components/dashboard/DashboardSummary";
import ActivitiesTable from "@/components/tables/dashboard/ActivitiesTable";
import { columns } from "@/components/tables/dashboard/columns";
import { useActivities } from "@/hooks/queries/useActivities";
import { useEffect } from "react";

export default function DashboardPage() {
  const { data: activities = [], isLoading } = useActivities();

  useEffect(() => {
    document.title = "Dashboard | Stockify";
  }, []);

  return (
    <main>
      <H1>Dashboard</H1>

      <div className="my-6 space-y-6">
        <DashboardSummary />

        <div className="my-6 grid grid-cols-1">
          {isLoading ? (
            <Spinner size="large" />
          ) : (
            <ActivitiesTable columns={columns} data={activities} />
          )}
        </div>
      </div>
    </main>
  );
}
