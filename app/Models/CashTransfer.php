<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CashTransfer extends Model
{
    protected $fillable = [
        'branch_id',
        'amount',
        'cash_before_transfer',
        'cash_after_transfer',
        'note',
        'status',
        'requested_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'cash_before_transfer' => 'decimal:2',
        'cash_after_transfer' => 'decimal:2',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
}
