<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'price',
        'unit_deduction_amount',
        'selling_rate',
        'discount_amount',
        'net_total',
    ];
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
