<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;
    protected $fillable = [
        'name',
        'rank',
        'branch_id',
        'father',
        'phone',
        'email',
        'address',
        'photo',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'branch_id');
    }
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}
