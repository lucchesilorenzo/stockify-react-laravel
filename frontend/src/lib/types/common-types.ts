export type FormDialogActionType =
  | "createOrder"
  | "createRestockOrder"
  | "editCustomer"
  | "addSupplier"
  | "addTask"
  | "editTask"
  | "generateTasks";

export type MonthlyInventoryValue = {
  id: string;
  month: Date;
  totalValue: number;
};
