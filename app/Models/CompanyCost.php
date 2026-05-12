<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCost extends Model
{
    use HasFactory;

    protected $fillable = [
        'amount',
        'cost_date',
        'category',
        'description',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'cost_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user who created the cost entry.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
