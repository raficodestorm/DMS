<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockCut extends Model
{
    protected $fillable = [
        'supplier_id',
        'requested_by',
        'branch_id',
        'net_total',
        'note',
        'status',
    ];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
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

