import { Category, Product, Task } from "@stockify/backend/types";
import { type ClassValue, clsx } from "clsx";
import { twMerge } from "tailwind-merge";

import { CustomerShipmentWithItems } from "./types";
import { Order } from "@stockify/backend/types";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

export function formatCurrency(amount: number) {
  return new Intl.NumberFormat("it-IT", {
    style: "currency",
    currency: "EUR",
  }).format(amount);
}

export function capitalize(text: string) {
  return text[0].toUpperCase() + text.slice(1).toLowerCase();
}

export function parseDates<T>(data: T): T {
  if (Array.isArray(data)) {
    return data.map((item) => parseDates(item)) as T;
  }

  if (data !== null && typeof data === "object") {
    return Object.fromEntries(
      Object.entries(data).map(([key, value]) => {
        if (
          (key === "createdAt" || key === "updatedAt") &&
          typeof value === "string"
        ) {
          return [key, new Date(value)];
        }
        return [key, parseDates(value)];
      }),
    ) as T;
  }

  return data;
}

export function formatOrderId(order: Order) {
  const date = order.createdAt.toISOString().split("T")[0].replace(/-/g, "");
  const numberPart = order.id.slice(-4).toUpperCase();

  return `ORD-${date}-${numberPart}`;
}

export function formatShipmentId(shipment: CustomerShipmentWithItems) {
  const date = shipment.createdAt.toISOString().split("T")[0].replace(/-/g, "");
  const numberPart = shipment.id.slice(-4).toUpperCase();

  return `SHIP-${date}-${numberPart}`;
}

export function formatTaskId(task: Task) {
  const numberPart = task.id.slice(-4).toUpperCase();

  return `TASK-${numberPart}`;
}

export function generateSKU({
  category,
  name,
}: Record<Category["name"], Product["name"]>) {
  const categoryPart = category.slice(0, 3).toUpperCase();
  const namePart = name.slice(0, 3).toUpperCase();
  const idPart = crypto.randomUUID().slice(-4).toUpperCase();

  return `${categoryPart}-${namePart}-${idPart}`;
}

export function generateSlug(text: string) {
  return text
    .toLowerCase()
    .trim()
    .replace(/\s+/g, "-")
    .replace(/[^\w-]+/g, "");
}
