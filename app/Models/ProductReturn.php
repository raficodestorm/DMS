<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductReturn extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'sr_id',
        'order_id',
        'total_amount',
        'reason',
        'status',
    ];
    public function items()
    {
        return $this->hasMany(ReturnItem::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sr()
    {
        return $this->belongsTo(User::class, 'sr_id');
    }
}
