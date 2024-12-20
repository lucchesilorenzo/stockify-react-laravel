import { Product } from "./product-types";

export type Warehouse = {
  id: number;
  name: string;
  location: string;
  quantity: number;
  maxQuantity: number;
  createdAt: Date;
  updatedAt: Date;
  products?: Product[];
};
