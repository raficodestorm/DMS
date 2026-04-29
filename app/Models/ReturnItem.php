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
    ];
}
