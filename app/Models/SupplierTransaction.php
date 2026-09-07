<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_id',
        'stock_in_request_id',
        'stock_cut_id',
        'branch_id',
        'type',
        'amount',
        'due_before_transaction',
        'due_after_transaction',
        'payment_method',
        'note'
    ];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function stock_in_request()
    {
        return $this->belongsTo(StockInRequest::class, 'stock_in_request_id');
    }

    public function stockCut()
    {
        return $this->belongsTo(StockCut::class, 'stock_cut_id');
    }

    public function stock_cut()
    {
        return $this->belongsTo(StockCut::class, 'stock_cut_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

}
