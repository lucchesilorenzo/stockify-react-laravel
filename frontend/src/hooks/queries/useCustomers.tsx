import { fetchData } from "@/lib/api-client";
import { CustomerWithCustomerShipment } from "@/lib/types/index";
import { useQuery } from "@tanstack/react-query";

export function useCustomers() {
  return useQuery({
    queryKey: ["customers"],
    queryFn: (): Promise<CustomerWithCustomerShipment[]> =>
      fetchData("/customers"),
  });
}
