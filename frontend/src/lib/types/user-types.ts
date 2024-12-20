import { Activity } from "./activity-types";
import { Order } from "./order-types";
import { Task } from "./task-types";

export type User = {
  id: number;
  email: string;
  password: string;
  firstName: string;
  lastName: string;
  dateOfBirth: Date | null;
  phone: string | null;
  bio: string | null;
  address: string | null;
  city: string | null;
  zipCode: string | null;
  createdAt: Date;
  updatedAt: Date;
  orders?: Order[];
  tasks?: Task[];
  activities?: Activity[];
};

export type UserEssentials = Pick<
  User,
  "email" | "password" | "firstName" | "lastName"
>;

export type UserSettings = {
  firstName: User["firstName"];
  lastName: User["lastName"];
  email: User["email"];
  dateOfBirth: User["dateOfBirth"] | null;
  bio: User["bio"] | null;
  phone: User["phone"] | null;
  address: User["address"] | null;
  city: User["city"] | null;
  zipCode: User["zipCode"] | null;
} | null;
