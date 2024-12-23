import { ProductEssentials } from "./product-types";
import { User } from "@/lib/types/index";

export type Activity = {
  id: string;
  userId: string;
  activity: string;
  entity: string;
  product: string | null;
  createdAt: Date;
  user?: User;
};

export type ActivityEssentials = {
  activity: "CREATED" | "UPDATED" | "DELETED" | "ARCHIVED" | "RESTORED";
  entity:
    | "Product"
    | "Order"
    | "Restock"
    | "Shipment"
    | "Customer"
    | "Supplier"
    | "Task";
  product?: ProductEssentials["name"];
  userId: User["id"];
};

export type DashboardActivity = Activity & {
  user: {
    firstName: string;
    lastName: string;
  };
};
