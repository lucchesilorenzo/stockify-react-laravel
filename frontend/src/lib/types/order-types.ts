import { Product } from "./product-types";
import { Supplier } from "./supplier-types";
import { User } from "./user-types";

export type Order = {
  id: string;
  userId: string;
  supplierId: string;
  productId: string;
  type: string;
  quantity: number;
  subtotal: number;
  shipping: number;
  vat: number;
  totalPrice: number;
  status: string;
  createdAt: Date;
  updatedAt: Date;
  user?: User;
  product?: Product;
  supplier?: Supplier;
};

export type OrderEssentials = Omit<
  Order,
  "id" | "createdAt" | "updatedAt" | "status"
>;

export type OrderType = {
  value: "NEW" | "RESTOCK";
  label: "New Orders" | "Restock Orders";
};

export type DetailedOrder = Order & {
  product: {
    name: string;
  };
  supplier: {
    name: string;
  };
  user: {
    firstName: string;
    lastName: string;
  };
};

export type OrderStatus = {
  value: "SHIPPED" | "DELIVERED";
  label: "Shipped" | "Delivered";
};
