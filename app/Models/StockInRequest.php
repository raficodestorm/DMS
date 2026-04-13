<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockInRequest extends Model
{
    protected $fillable = [
        'supplier_id',
        'requested_by',
        'net_total',
        'status',
        'approved_by',
        'note',
    ];
    // ১. কে রিকোয়েস্ট করেছে (Manager)
    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    // ২. কে এপ্রুভ করেছে (Admin)
    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // ৩. কোন সাপ্লায়ারের কাছ থেকে আসছে
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    // ৪. এই রিকোয়েস্টের আন্ডারে কি কি প্রোডাক্ট আছে
    public function items()
    {
        return $this->hasMany(StockInItem::class, 'stock_in_request_id');
    }
}
