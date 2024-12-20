import { Category } from "./category-types";
import { ShipmentItem } from "./customer-types";
import { Order } from "./order-types";
import { Warehouse } from "./warehouse-types";

export type Product = {
  id: number;
  categoryId: number;
  warehouseId: number;
  name: string;
  slug: string;
  sku: string;
  price: number;
  quantity: number;
  maxQuantity: number;
  vatRate: number;
  description: string | null;
  status: string;
  image: string;
  createdAt: Date;
  updatedAt: Date;
  category?: Category;
  warehouse?: Warehouse;
  orders?: Order[];
  shipmentItems?: ShipmentItem[];
};

export type ProductWithCategoryAndWarehouse = Product & {
  category: {
    name: string;
  };
  warehouse: {
    name: string;
  };
};

export type ProductEssentials = Omit<
  Product,
  "id" | "createdAt" | "updatedAt" | "status" | "description" | "image"
>;

export type ProductStatus = {
  value: "IN_STOCK" | "OUT_OF_STOCK" | "ARCHIVED";
  label: "In Stock" | "Out of Stock" | "Archived";
};
