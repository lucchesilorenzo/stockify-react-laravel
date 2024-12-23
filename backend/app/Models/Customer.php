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
        'first_name',
        'last_name',
        'email',
        'phone',
        'address',
        'city',
        'zip_code',
    ];

    /**
     * Get all of the customer's shipments.
     *
     * @return HasMany
     */
    public function customerShipments(): HasMany
    {
        return $this->hasMany(CustomerShipment::class);
    }
}
