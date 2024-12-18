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
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['userId']);
            $table->dropColumn('userId');

            $table->foreignId('userId')->constrained()->onDelete('cascade');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['userId']);
            $table->dropColumn('userId');

            $table->foreignId('userId')->constrained()->onDelete('cascade');
        });

        Schema::table('customer_shipments', function (Blueprint $table) {
            $table->dropForeign(['customerId']);
            $table->dropColumn('customerId');

            $table->foreignId('customerId')->constrained()->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['userId']);
            $table->dropColumn('userId');

            $table->dropForeign(['supplierId']);
            $table->dropColumn('supplierId');

            $table->dropForeign(['productId']);
            $table->dropColumn('productId');

            $table->foreignId('userId')->constrained()->onDelete('cascade');
            $table->foreignId('supplierId')->constrained()->onDelete('cascade');
            $table->foreignId('productId')->constrained()->onDelete('cascade');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['categoryId']);
            $table->dropColumn('categoryId');

            $table->dropForeign(['warehouseId']);
            $table->dropColumn('warehouseId');

            $table->foreignId('categoryId')->constrained()->onDelete('cascade');
            $table->foreignId('warehouseId')->constrained()->onDelete('cascade');
        });

        Schema::table('shipment_items', function (Blueprint $table) {
            $table->dropForeign(['productId']);
            $table->dropColumn('productId');

            $table->dropForeign(['customerShipmentId']);
            $table->dropColumn('customerShipmentId');

            $table->foreignId('productId')->constrained()->onDelete('cascade');
            $table->foreignId('customerShipmentId')->constrained()->onDelete('cascade');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('website')->nullable()->unique()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['userId']);
            $table->dropColumn('userId');

            $table->foreignUuid('userId')->constrained()->onDelete('cascade');
        });

        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['userId']);
            $table->dropColumn('userId');

            $table->foreignUuid('userId')->constrained()->onDelete('cascade');
        });

        Schema::table('customer_shipments', function (Blueprint $table) {
            $table->dropForeign(['customerId']);
            $table->dropColumn('customerId');

            $table->foreignUuid('customerId')->constrained()->onDelete('cascade');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropForeign(['userId']);
            $table->dropColumn('userId');

            $table->dropForeign(['supplierId']);
            $table->dropColumn('supplierId');

            $table->dropForeign(['productId']);
            $table->dropColumn('productId');

            $table->foreignUuid('userId')->constrained()->onDelete('cascade');
            $table->foreignUuid('supplierId')->constrained()->onDelete('cascade');
            $table->foreignUuid('productId')->constrained()->onDelete('cascade');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['categoryId']);
            $table->dropColumn('categoryId');

            $table->dropForeign(['warehouseId']);
            $table->dropColumn('warehouseId');

            $table->foreignUuid('categoryId')->constrained()->onDelete('cascade');
            $table->foreignUuid('warehouseId')->constrained()->onDelete('cascade');
        });

        Schema::table('shipment_items', function (Blueprint $table) {
            $table->dropForeign(['productId']);
            $table->dropColumn('productId');

            $table->dropForeign(['customerShipmentId']);
            $table->dropColumn('customerShipmentId');

            $table->foreignUuid('productId')->constrained()->onDelete('cascade');
            $table->foreignUuid('customerShipmentId')->constrained()->onDelete('cascade');
        });

        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropUnique(['website']);
            $table->string('website')->nullable()->change();
        });
    }
};
