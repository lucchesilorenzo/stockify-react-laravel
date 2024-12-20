export type FormDialogActionType =
  | "createOrder"
  | "createRestockOrder"
  | "editCustomer"
  | "addSupplier"
  | "addTask"
  | "editTask"
  | "generateTasks";

export type MonthlyInventoryValue = {
  id: number;
  month: Date;
  totalValue: number;
};
