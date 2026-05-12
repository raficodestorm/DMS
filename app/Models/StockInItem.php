<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInItem extends Model
{
    protected $fillable = [
        'stock_in_request_id',
        'product_id',
        'quantity',
        'cost_price',
        'tree_deduction',

    ];
    // ১. এই আইটেমটি কোন প্রোডাক্ট
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // ২. এটি কোন রিকোয়েস্টের অংশ
    public function stockInRequest()
    {
        return $this->belongsTo(StockInRequest::class, 'stock_in_request_id');
    }
}
