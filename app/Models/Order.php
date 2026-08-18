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
        'special_discount',
        'discount_amount',
        'net_total',
        'applied_deduction_percent',
        'note',
        'branch_id',
        'order_type',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sr()
    {
        return $this->belongsTo(User::class, 'sr_id');
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function manager()
    {
        return $this->belongsTo(User::class, 'manager_id');
    }
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }
    public function returns()
    {
        return $this->hasMany(ProductReturn::class);
    }
}
