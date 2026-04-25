<?php

namespace App\Models;

use App\Models\Stock;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Branch extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'name',
        'manager',
        'address',
    ];
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }
    public function orders()
    {
        return $this->hasMany(Order::class, 'branch_id');
    }
    public function users()
    {
        return $this->hasMany(User::class, 'branch_id');
    }
}
