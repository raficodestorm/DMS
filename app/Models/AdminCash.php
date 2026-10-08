<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminCash extends Model
{
    use HasFactory;

    protected $fillable = [
        'admin_id',
        'cash',
    ];

    protected $casts = [
        'cash' => 'decimal:2',
    ];

    public function admin()
{
    return $this->belongsTo(User::class, 'admin_id');
}

    

    
}
