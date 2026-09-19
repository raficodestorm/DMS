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

    public function stocks()
    {
        return $this->hasMany(Stock::class);
    }

    public function getTotalStockAttribute(): int
    {
        if (array_key_exists('stocks_sum_quantity', $this->attributes)) {
            return (int) ($this->attributes['stocks_sum_quantity'] ?? 0);
        }
        if (array_key_exists('stock_sum_quantity', $this->attributes)) {
            return (int) ($this->attributes['stock_sum_quantity'] ?? 0);
        }
        if ($this->relationLoaded('stocks')) {
            return (int) $this->stocks->sum('quantity');
        }
        if ($this->relationLoaded('stock')) {
            return (int) $this->stock->sum('quantity');
        }
        return (int) $this->stocks()->sum('quantity');
    }

    public function getIsInStockAttribute(): bool
    {
        return $this->total_stock > 0;
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function activeRetailOffer()
    {
        $today = now()->toDateString();
        return $this->hasOne(Offer::class)
            ->where('status', 1)
            ->where('customer_type', 'retail')
            ->whereDate('start_date', '<=', $today)
            ->whereDate('end_date', '>=', $today);
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
