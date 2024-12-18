import { fetchData } from "@/lib/api-client";
import { ProductWithCategoryAndWarehouse } from "@/lib/types/index";
import { useQuery } from "@tanstack/react-query";

type useProductProps = {
  productSlug?: string;
};

export function useProduct({ productSlug }: useProductProps) {
  return useQuery({
    queryKey: ["product", productSlug],
    queryFn: (): Promise<ProductWithCategoryAndWarehouse> =>
      fetchData(`/products/slug/${productSlug}`),
    enabled: !!productSlug,
  });
}
