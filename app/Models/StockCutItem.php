<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCutItem extends Model
{
    protected $fillable = [
        'stock_cut_id',
        'product_id',
        'quantity',
        'price',
    ];

    public function stockCut()
    {
        return $this->belongsTo(StockCut::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

