<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;

class MonthlyInventoryValue extends Model
{
    use HasUuids;
    /**
     * The attributes that are mass assignable.
     * 
     * @var array
     */
    protected $fillable = [
        'month',
        'total_value'
    ];

    public $timestamps = false;
}
