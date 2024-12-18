<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('firstName', 'first_name');
            $table->renameColumn('lastName', 'last_name');
            $table->renameColumn('dateOfBirth', 'date_of_birth');
            $table->renameColumn('zipCode', 'zip_code');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('userId', 'user_id');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('dueDate', 'due_date');
            $table->renameColumn('userId', 'user_id');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('firstName', 'first_name');
            $table->renameColumn('lastName', 'last_name');
            $table->renameColumn('zipCode', 'zip_code');
        });

        Schema::table('customer_shipments', function (Blueprint $table) {
            $table->renameColumn('customerId', 'customer_id');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('totalPrice', 'total_price');
            $table->renameColumn('userId', 'user_id');
            $table->renameColumn('supplierId', 'supplier_id');
            $table->renameColumn('productId', 'product_id');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('maxQuantity', 'max_quantity');
            $table->renameColumn('vatRate', 'vat_rate');
            $table->renameColumn('categoryId', 'category_id');
            $table->renameColumn('warehouseId', 'warehouse_id');
        });

        Schema::table('shipment_items', function (Blueprint $table) {
            $table->renameColumn('productId', 'product_id');
            $table->renameColumn('customerShipmentId', 'customer_shipment_id');
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->renameColumn('maxQuantity', 'max_quantity');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->renameColumn('zipCode', 'zip_code');
        });

        Schema::table('monthly_inventory_values', function (Blueprint $table) {
            $table->renameColumn('totalValue', 'total_value');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->renameColumn('first_name', 'firstName');
            $table->renameColumn('last_name', 'lastName');
            $table->renameColumn('date_of_birth', 'dateOfBirth');
            $table->renameColumn('zip_code', 'zipCode');
        });

        Schema::table('activities', function (Blueprint $table) {
            $table->renameColumn('user_id', 'userId');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->renameColumn('due_date', 'dueDate');
            $table->renameColumn('user_id', 'userId');
        });

        Schema::table('customers', function (Blueprint $table) {
            $table->renameColumn('first_name', 'firstName');
            $table->renameColumn('last_name', 'lastName');
            $table->renameColumn('zip_code', 'zipCode');
        });

        Schema::table('customer_shipments', function (Blueprint $table) {
            $table->renameColumn('customer_id', 'customerId');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->renameColumn('total_price', 'totalPrice');
            $table->renameColumn('user_id', 'userId');
            $table->renameColumn('supplier_id', 'supplierId');
            $table->renameColumn('product_id', 'productId');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->renameColumn('max_quantity', 'maxQuantity');
            $table->renameColumn('vat_rate', 'vatRate');
            $table->renameColumn('category_id', 'categoryId');
            $table->renameColumn('warehouse_id', 'warehouseId');
        });

        Schema::table('shipment_items', function (Blueprint $table) {
            $table->renameColumn('product_id', 'productId');
            $table->renameColumn('customer_shipment_id', 'customerShipmentId');
        });

        Schema::table('warehouses', function (Blueprint $table) {
            $table->renameColumn('max_quantity', 'maxQuantity');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->renameColumn('zip_code', 'zipCode');
        });

        Schema::table('monthly_inventory_values', function (Blueprint $table) {
            $table->renameColumn('total_value', 'totalValue');
        });
    }
};
