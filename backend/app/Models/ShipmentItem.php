<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShipmentItem extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'customer_shipment_id',
        'quantity',
    ];

    public $timestamps = false;

    /**
     * Get the product that owns the shipment item.
     *
     * @return BelongsTo
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get the customer shipment that owns the shipment item.
     *
     * @return BelongsTo
     */
    public function customerShipment(): BelongsTo
    {
        return $this->belongsTo(CustomerShipment::class);
    }
}
