<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Offer extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'product_id',
        'type',
        'discount_amount',
        'start_date',
        'end_date',
        'status',
    ];
}
