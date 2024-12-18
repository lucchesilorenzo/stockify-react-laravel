import { postData } from "@/lib/api-client";
import { useMutation } from "@tanstack/react-query";
import { toast } from "sonner";

export function useLogOut() {
  return useMutation({
    mutationFn: () => postData("/logout"),
    onError: (error) => {
      toast.error(error.message);
    },
  });
}
