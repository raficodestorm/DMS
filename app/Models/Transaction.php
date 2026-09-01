<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'customer_id',
        'order_id',
        'sr_id',
        'branch_id',
        'type',
        'amount',
        'due_before_transaction',
        'due_after_transaction',
        'payment_method',
        'status',
        'note'
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function sr()
    {
        return $this->belongsTo(User::class, 'sr_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
