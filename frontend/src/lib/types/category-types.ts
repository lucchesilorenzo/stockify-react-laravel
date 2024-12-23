import { Product } from "./product-types";

export type Category = {
  id: string;
  name: string;
  createdAt: Date;
  updatedAt: Date;
  products?: Product[];
};

export type CategoryVATRates = {
  value: "4" | "10" | "22";
  label: "4%" | "10%" | "22%";
};
