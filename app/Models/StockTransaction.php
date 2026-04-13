<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransaction extends Model
{
    protected $fillable = [
        'product_id',
        'type',
        'quantity',
        'reference_id',
        'reference_type',
        'status',
        'requested_by',
        'approved_by',
    ];
}
