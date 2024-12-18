import { Order } from "./order-types";

export type Supplier = {
  id: string;
  name: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  zipCode: string;
  website: string | null;
  rating: number;
  createdAt: Date;
  updatedAt: Date;
  orders?: Order[];
};

export type SupplierWithOrderCount = Supplier & {
  _count: {
    orders: number;
  };
};
