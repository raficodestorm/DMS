<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Slide extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'position',
        'offer_id',
        'custom_url',
        'image',
        'status',
    ];

    protected $casts = [
        'status'   => 'boolean',
        'position' => 'integer',
    ];

    /**
     * Many-to-many relationship with offers.
     */
    public function offers(): BelongsToMany
    {
        return $this->belongsToMany(Offer::class, 'offer_slide');
    }

    /**
     * Legacy single offer relationship (for backwards compatibility).
     */
    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    /**
     * Scope for active slides.
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    /**
     * Scope for ordering by position.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('position', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Dynamic target URL attribute for click redirect.
     */
    public function getTargetUrlAttribute(): string
    {
        if (!empty($this->custom_url)) {
            return $this->custom_url;
        }

        // If offers are attached via many-to-many relationship
        if ($this->relationLoaded('offers') ? $this->offers->isNotEmpty() : $this->offers()->exists()) {
            return route('slide.offers', $this->id);
        }

        // Fallback for single offer relation
        if ($this->offer_id && ($this->relationLoaded('offer') ? $this->offer : $this->offer()->exists())) {
            return route('slide.offers', $this->id);
        }

        return route('shop');
    }
}
