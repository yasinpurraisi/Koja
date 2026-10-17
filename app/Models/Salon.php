<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Salon extends Model
{
    use HasFactory;

    protected $fillable = [
        'owner_id',
        'name',
        'description',
        'phone',
        'address',
        'city',
        'latitude',
        'longitude',
        'status',
        'approved_by',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'reviewed_at' => 'datetime',
        ];
    }

    // The user who owns this salon.
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    // The administrator who last reviewed this salon.
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // All reviews submitted for this salon.
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    // The salon's weekly opening schedule.
    public function openingHours(): HasMany
    {
        return $this->hasMany(SalonOpeningHour::class);
    }
}