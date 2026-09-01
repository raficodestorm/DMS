<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use HasFactory;
    protected $fillable = [
        'product_return_id',
        'product_id',
        'quantity',
        'price',
        'subtotal',
        'profit',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productReturn()
    {
        return $this->belongsTo(ProductReturn::class);
    }
}
