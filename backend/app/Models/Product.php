<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Product extends Model
{
    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id',
        'warehouse_id',
        'name',
        'slug',
        'sku',
        'price',
        'quantity',
        'max_quantity',
        'vat_rate',
        'description',
        'status',
        'image',
    ];

    /**
     * Get the category that owns the product.
     *
     * @return BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the warehouse that owns the product.
     *
     * @return BelongsTo
     */
    public function warehouse(): BelongsTo
    {
        return $this->belongsTo(Warehouse::class);
    }

    /**
     * Get all of the orders associated with the product.
     *
     * @return HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Get all of the shipment items associated with the product.
     *
     * @return HasMany
     */
    public function shipmentItems(): HasMany
    {
        return $this->hasMany(ShipmentItem::class);
    }
}
