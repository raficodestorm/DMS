<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'order_id',
        'customer_id',
        'sr_id',
        'manager_id',
        'status',
        'special_discount',
        'discount_amount',
        'net_total',
        'applied_deduction_percent',
        'note',
        'delivered_by',
        'delivered_at',
        'branch_id',
        'order_type',
        'payment_status',
        'payment_amount',
        'customer_name',
        'customer_phone',
        'country',
        'city',
        'address',
        'shipping_charge',
        'payment_method',
    ];

    protected $casts = [
        'delivered_at' => 'datetime',
    ];

    public function getDisplayOrderIdAttribute(): string
    {
        return $this->order_id ?? ('BRS' . $this->id);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sr()
    {
        return $this->belongsTo(User::class, 'sr_id');
    }

    public function dso()
    {
        return $this->belongsTo(User::class, 'delivered_by');
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
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
