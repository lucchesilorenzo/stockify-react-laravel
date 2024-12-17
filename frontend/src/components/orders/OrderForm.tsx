import { zodResolver } from "@hookform/resolvers/zod";
import { useForm } from "react-hook-form";

import { LoadingButton } from "../common/LoadingButton";
import { Input } from "../ui/input";
import { Label } from "../ui/label";

import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select";
import { useCreateOrder } from "@/hooks/mutations/orders/useCreateOrder";
import { useProduct } from "@/hooks/useProduct";
import {
  TOrderFormSchema,
  orderFormSchema,
} from "@/lib/validations/order-validations";
import { categoryVATRates } from "@/lib/data";
import { useFontSize } from "@/hooks/useFontSize";
import { cn } from "@/lib/utils";

type OrderFormProps = {
  onFormSubmit: () => void;
};

export default function OrderForm({ onFormSubmit }: OrderFormProps) {
  const { fontSize } = useFontSize();
  const { categories, warehouses } = useProduct();
  const { suppliers } = useProduct();
  const { mutateAsync: createOrder } = useCreateOrder();

  const {
    register,
    handleSubmit,
    setValue,
    formState: { isSubmitting, errors },
  } = useForm<TOrderFormSchema>({
    resolver: zodResolver(orderFormSchema),
  });

  const space: Record<string, { spaceY: string }> = {
    "text-md": { spaceY: "space-y-6" },
    "text-lg": { spaceY: "space-y-4" },
    "text-xl": { spaceY: "space-y-2" },
  };

  async function onSubmit(data: TOrderFormSchema) {
    await createOrder(data);
    onFormSubmit();
  }

  return (
    <form onSubmit={handleSubmit(onSubmit)} className="flex flex-col">
      <div className={cn(space[fontSize].spaceY)}>
        <div className="text-sm text-green-600">
          Orders exceeding 50,00 € in product value qualify for free shipping.
        </div>

        <div className="space-y-1">
          <Label htmlFor="name">
            Name <span className="text-red-600">*</span>
          </Label>
          <Input
            id="name"
            placeholder="Enter product name"
            {...register("name")}
          />
          {errors.name && (
            <p className="px-1 text-sm text-red-600">{errors.name.message}</p>
          )}
        </div>

        <div className="grid grid-cols-2 gap-4">
          <div className="space-y-1">
            <Label htmlFor="categoryId">
              Category <span className="text-red-600">*</span>
            </Label>
            <Select onValueChange={(value) => setValue("categoryId", value)}>
              <SelectTrigger id="categoryId">
                <SelectValue placeholder="Select a category" />
              </SelectTrigger>
              <SelectContent>
                {categories.map((category) => (
                  <SelectItem key={category.id} value={category.id}>
                    {category.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.categoryId && (
              <p className="px-1 text-sm text-red-600">
                {errors.categoryId.message}
              </p>
            )}
          </div>

          <div className="space-y-1">
            <Label htmlFor="vatRate">
              VAT rate (%) <span className="text-red-600">*</span>
            </Label>
            <Select onValueChange={(value) => setValue("vatRate", value)}>
              <SelectTrigger id="vatRate">
                <SelectValue placeholder="Select a VAT rate" />
              </SelectTrigger>
              <SelectContent>
                {categoryVATRates.map((vatRate) => (
                  <SelectItem key={vatRate.value} value={vatRate.value}>
                    {vatRate.label}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.vatRate && (
              <p className="px-1 text-sm text-red-600">
                {errors.vatRate.message}
              </p>
            )}
          </div>
        </div>

        <div className="grid grid-cols-2 gap-4">
          <div className="space-y-1">
            <Label htmlFor="warehouseId">
              Warehouse <span className="text-red-600">*</span>
            </Label>
            <Select onValueChange={(value) => setValue("warehouseId", value)}>
              <SelectTrigger id="warehouseId">
                <SelectValue placeholder="Select a warehouse" />
              </SelectTrigger>
              <SelectContent>
                {warehouses.map((warehouse) => (
                  <SelectItem key={warehouse.id} value={warehouse.id}>
                    {warehouse.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.warehouseId && (
              <p className="px-1 text-sm text-red-600">
                {errors.warehouseId.message}
              </p>
            )}
          </div>

          <div className="space-y-1">
            <Label htmlFor="supplierId">
              Supplier <span className="text-red-600">*</span>
            </Label>
            <Select onValueChange={(value) => setValue("supplierId", value)}>
              <SelectTrigger id="supplierId">
                <SelectValue placeholder="Select a supplier" />
              </SelectTrigger>
              <SelectContent>
                {suppliers.map((supplier) => (
                  <SelectItem key={supplier.id} value={supplier.id}>
                    {supplier.name}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
            {errors.supplierId && (
              <p className="px-1 text-sm text-red-600">
                {errors.supplierId.message}
              </p>
            )}
          </div>
        </div>

        <div className="space-y-1">
          <Label htmlFor="price">
            Base price (€) <span className="text-red-600">*</span>
          </Label>
          <Input id="price" placeholder="0.00" {...register("price")} />
          {errors.price && (
            <p className="px-1 text-sm text-red-600">{errors.price.message}</p>
          )}
        </div>

        <div className="grid grid-cols-2 gap-4">
          <div className="space-y-1">
            <Label htmlFor="quantity">
              Quantity <span className="text-red-600">*</span>
            </Label>
            <Input id="quantity" placeholder="1" {...register("quantity")} />
            {errors.quantity && (
              <p className="px-1 text-sm text-red-600">
                {errors.quantity.message}
              </p>
            )}
          </div>

          <div className="space-y-1">
            <Label htmlFor="maxQuantity">
              Max quantity <span className="text-red-600">*</span>
            </Label>
            <Input
              id="maxQuantity"
              placeholder="100"
              {...register("maxQuantity")}
            />
            {errors.maxQuantity && (
              <p className="px-1 text-sm text-red-600">
                {errors.maxQuantity.message}
              </p>
            )}
          </div>
        </div>

        <LoadingButton isLoading={isSubmitting} className="w-full">
          Create
        </LoadingButton>
      </div>
    </form>
  );
}
