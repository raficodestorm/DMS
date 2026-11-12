<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminCashLedger extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'type',
        'amount',
        'cash_before',
        'cash_after',
        'payment_method',
        'status',
        'reference_type',
        'reference_id',
        'note',
    ];

    protected $casts = [
        'amount'      => 'decimal:2',
        'cash_before' => 'decimal:2',
        'cash_after'  => 'decimal:2',
    ];

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}

