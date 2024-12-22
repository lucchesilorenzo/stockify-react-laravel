import { postData } from "@/lib/api-client";
import { useMutation, useQueryClient } from "@tanstack/react-query";
import { toast } from "sonner";

type UpdateProduct = {
  formData: FormData;
  productId: number;
};

export function useUpdateProduct({ productSlug }: { productSlug: string }) {
  const queryClient = useQueryClient();

  return useMutation({
    mutationFn: ({ formData, productId }: UpdateProduct) =>
      // Update product with POST because PHP doesn't support PATCH/PUT with FormData
      postData(`/products/${productId}`, formData),
    onSuccess: (response) => {
      queryClient.invalidateQueries({ queryKey: ["product", productSlug] });
      toast.success(response.message);
    },
    onError: (error) => {
      toast.error(error.message);
    },
  });
}
