<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Hotel extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'address',
        'city',
        'state',
        'country',
        'postal_code',
        'phone',
        'email',
        'website',
        'latitude',
        'longitude',
        'amenities',
        'images',
        'star_rating',
        'is_active',
    ];

    protected $casts = [
        'amenities' => 'array',
        'images' => 'array',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'star_rating' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($hotel) {
            if (empty($hotel->slug)) {
                $hotel->slug = Str::slug($hotel->name);
            }
        });

        static::updating(function ($hotel) {
            if ($hotel->isDirty('name') && empty($hotel->slug)) {
                $hotel->slug = Str::slug($hotel->name);
            }
        });
    }

    /**
     * Get the rooms for the hotel.
     */
    public function rooms(): HasMany
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Get active rooms for the hotel.
     */
    public function activeRooms(): HasMany
    {
        return $this->rooms()->where('is_active', true);
    }

    /**
     * Get available rooms for the hotel.
     */
    public function availableRooms(): HasMany
    {
        return $this->rooms()->where('is_active', true)->where('is_available', true);
    }

    /**
     * Scope a query to only include active hotels.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the full address of the hotel.
     */
    public function getFullAddressAttribute(): string
    {
        $address = $this->address;
        if ($this->city) {
            $address .= ', ' . $this->city;
        }
        if ($this->state) {
            $address .= ', ' . $this->state;
        }
        if ($this->country) {
            $address .= ', ' . $this->country;
        }
        if ($this->postal_code) {
            $address .= ' ' . $this->postal_code;
        }
        return $address;
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
