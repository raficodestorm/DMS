<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;
    protected $fillable = [
        'shop_name',
        'manager',
        'phone',
        'address',
        'branch_id',
        'due',
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
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
