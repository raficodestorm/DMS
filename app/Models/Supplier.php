<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'company_name',
        'phone',
        'email',
        'address',
        'due',
        'image',
    ];
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function deductions()
    {
        return $this->hasMany(Deduction::class);
    }

    public function deduction()
    {
        return $this->hasOne(Deduction::class)->latestOfMany();
    }

    public function stockRequests()
    {
        return $this->hasMany(StockInRequest::class);
    }
}
