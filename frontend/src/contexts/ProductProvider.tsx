import { createContext } from "react";

import {
  Category,
  Warehouse,
  ProductWithCategoryAndWarehouse,
  SupplierWithOrderCount,
} from "@/lib/types/index";

type ProductProviderProps = {
  children: React.ReactNode;
  products: ProductWithCategoryAndWarehouse[];
  categories: Category[];
  warehouses: Warehouse[];
  suppliers: SupplierWithOrderCount[];
};

type TProductContext = {
  products: ProductWithCategoryAndWarehouse[];
  categories: Category[];
  warehouses: Warehouse[];
  suppliers: SupplierWithOrderCount[];
};

export const ProductContext = createContext<TProductContext | null>(null);

export default function ProductProvider({
  children,
  products,
  categories,
  warehouses,
  suppliers,
}: ProductProviderProps) {
  return (
    <ProductContext.Provider
      value={{
        products,
        categories,
        warehouses,
        suppliers,
      }}
    >
      {children}
    </ProductContext.Provider>
  );
}
