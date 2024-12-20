import { Product } from "./product-types";

export type Customer = {
  id: number;
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  zipCode: string;
  createdAt: Date;
  updatedAt: Date;
  customerShipments?: CustomerShipment[];
};

export type CustomerEssentials = Omit<
  Customer,
  "id" | "createdAt" | "updatedAt"
>;

export type CustomerShipment = {
  id: number;
  customerId: number;
  status: string;
  createdAt: Date;
  updatedAt: Date;
  customer?: Customer;
  shipmentItems?: ShipmentItem[];
};

export type ShipmentItem = {
  id: number;
  productId: number;
  customerShipmentId: number;
  quantity: number;
  product?: Product;
  customerShipment?: CustomerShipment;
};

export type CustomerWithCustomerShipment = {
  id: number;
  firstName: string;
  lastName: string;
  email: string;
  phone: string;
  address: string;
  city: string;
  zipCode: string;
  createdAt: Date;
  updatedAt: Date;
  customerShipments: {
    id: number;
    customerId: number;
    status: string;
    createdAt: Date;
    updatedAt: Date;
    shipmentItems: {
      id: number;
      productId: number;
      customerShipmentId: number;
      quantity: number;
      product: {
        name: string;
        price: number;
      };
    }[];
  }[];
};

export type CustomerShipmentWithItems = {
  id: number;
  customerId: number;
  status: string;
  createdAt: Date;
  updatedAt: Date;
  shipmentItems: {
    id: number;
    productId: number;
    customerShipmentId: number;
    quantity: number;
    product: {
      name: string;
      price: number;
    };
  }[];
};

export type CustomerSelectedProduct = {
  productId: number;
  warehouseId: number;
  name: string;
  price: number;
  quantity: number;
  maxQuantity: number;
};

export type CustomerShipmentEssentials = Omit<
  CustomerShipment,
  "id" | "createdAt" | "updatedAt" | "status"
>;
