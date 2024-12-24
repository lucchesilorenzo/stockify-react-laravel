import { postData } from "@/lib/api-client";
import { useMutation } from "@tanstack/react-query";
import { toast } from "sonner";

export function useLogOut() {
  return useMutation({
    mutationFn: () => postData("/auth/logout"),
    onError: (error) => {
      toast.error(error.message);
    },
  });
}
