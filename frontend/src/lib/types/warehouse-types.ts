import { Product } from "./product-types";

export type Warehouse = {
  id: string;
  name: string;
  location: string;
  quantity: number;
  maxQuantity: number;
  createdAt: Date;
  updatedAt: Date;
  products?: Product[];
};
