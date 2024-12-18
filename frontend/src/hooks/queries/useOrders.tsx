import { fetchData } from "@/lib/api-client";
import { DetailedOrder, Order } from "@/lib/types/index";
import { useQueries } from "@tanstack/react-query";

export function useOrders() {
  return useQueries({
    queries: [
      {
        queryKey: ["orders"],
        queryFn: (): Promise<DetailedOrder[]> => fetchData("/orders"),
      },
      {
        queryKey: ["orders", "monthly"],
        queryFn: (): Promise<Order[]> => fetchData("/orders/monthly"),
      },
      {
        queryKey: ["orders", "weekly"],
        queryFn: (): Promise<Order[]> => fetchData("/orders/weekly"),
      },
    ],
  });
}
