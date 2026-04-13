<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cost extends Model
{
    protected $fillable = [
        'user_id',
        'amount',
        'purpose',
        'staff_name',
        'details',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
