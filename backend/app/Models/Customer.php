<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'firstName',
        'lastName',
        'email',
        'phone',
        'address',
        'city',
        'zipCode',
    ];

    /**
     * Get all of the customer's shipments.
     *
     * @return HasMany
     */
    public function customerShipments(): HasMany
    {
        return $this->hasMany(Shipment::class);
    }
}
