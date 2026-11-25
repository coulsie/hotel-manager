<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConferenceSpace extends Model
{
    use HasFactory;

    protected $table = 'conference_spaces';

    protected $fillable = [
        'name',
        'category',
        'capacity',
        'hourly_rate',
        'half_day_rate',
        'full_day_rate',
        'equipments',
        'is_active',
    ];

    /**
     * Réservations associées à cet espace.
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(SpaceBooking::class, 'space_id');
    }
}
