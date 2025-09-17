<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RoomCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'max_occupancy',
        'base_price',
        'amenities',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'amenities' => 'array',
            'base_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the rooms for this category
     */
    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    /**
     * Scope to get only active categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
