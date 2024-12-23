import { Product } from "./product-types";

export type Customer = {
  id: string;
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
  id: string;
  customerId: string;
  status: string;
  createdAt: Date;
  updatedAt: Date;
  customer?: Customer;
  shipmentItems?: ShipmentItem[];
};

export type ShipmentItem = {
  id: string;
  productId: string;
  customerShipmentId: string;
  quantity: number;
  product?: Product;
  customerShipment?: CustomerShipment;
};

export type CustomerWithCustomerShipment = {
  id: string;
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
    id: string;
    customerId: string;
    status: string;
    createdAt: Date;
    updatedAt: Date;
    shipmentItems: {
      id: string;
      productId: string;
      customerShipmentId: string;
      quantity: number;
      product: {
        name: string;
        price: number;
      };
    }[];
  }[];
};

export type CustomerShipmentWithItems = {
  id: string;
  customerId: string;
  status: string;
  createdAt: Date;
  updatedAt: Date;
  shipmentItems: {
    id: string;
    productId: string;
    customerShipmentId: string;
    quantity: number;
    product: {
      name: string;
      price: number;
    };
  }[];
};

export type CustomerSelectedProduct = {
  productId: string;
  warehouseId: string;
  name: string;
  price: number;
  quantity: number;
  maxQuantity: number;
};

export type CustomerShipmentEssentials = Omit<
  CustomerShipment,
  "id" | "createdAt" | "updatedAt" | "status"
>;
