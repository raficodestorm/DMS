<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Deduction extends Model
{
    use HasFactory;
    protected $fillable = [
        'type',
        'customer_deduction',
        'my_deduction',
        'tree_deduction',
        'floor_deduction',
    ];
}
