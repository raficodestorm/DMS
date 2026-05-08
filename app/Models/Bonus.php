<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bonus extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'amount',
        'bonus_date',
        'type',
        'description',
        'created_by',
    ];

    protected $casts = [
        'bonus_date' => 'date',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the user who created the bonus entry.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
