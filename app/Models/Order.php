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
        'total',
        'discount_amount',
        'net_total',
        'note',
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
}
