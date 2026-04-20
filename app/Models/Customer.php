<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_name',
        'manager',
        'phone',
        'address',
        'branch_id',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    public function users()
    {
        return $this->hasMany(User::class);
    }
    public function order()
    {
        return $this->hasMany(Order::class);
    }
}
