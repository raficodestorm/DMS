<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCut extends Model
{
    protected $fillable = [
        'supplier_id',
        'requested_by',
        'net_total',
        'note',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function requestedBy()
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function items()
    {
        return $this->hasMany(StockCutItem::class);
    }
}

