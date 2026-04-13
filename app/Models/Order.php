<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'customer_id',
        'sr_id',
        'manager_id',
        'status',
        'total',
        'discount_amount',
        'net_total',
        'note',
    ];
}
