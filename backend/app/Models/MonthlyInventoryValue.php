<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlyInventoryValue extends Model
{
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
