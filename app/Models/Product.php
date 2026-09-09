<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'sku',
        'barcode',
        'category_id',
        'supplier_id',
        'price',
        'purchase_price',
        'stock_alert',
        'weight',
        'length',
        'height',
        'width',
        'short_description',
        'long_description',
        'status',
        'image',
        'is_featured',
    ];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function stock()
{
    return $this->hasMany(Stock::class);
}

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function offers()
    {
        return $this->belongsToMany(Offer::class, 'offer_products');
    }
    public function stockItems()
    {
        return $this->hasMany(StockInItem::class);

    }
    public function images()
    {
        return $this->hasMany(ProductImage::class)
            ->orderBy('sort_order');
    }
}
