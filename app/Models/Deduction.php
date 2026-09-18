<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    use HasFactory;
    protected $fillable = [
        'supplier_id',
        'type',
        'customer_deduction',
        'retail_deduction',
        'my_deduction',
        'tree_deduction',
        'floor_deduction',
    ];
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }
}
