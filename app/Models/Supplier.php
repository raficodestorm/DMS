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

    public function stockRequests()
    {
        return $this->hasMany(StockInRequest::class);
    }
}
