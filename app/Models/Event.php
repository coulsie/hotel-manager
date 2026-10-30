<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    protected $fillable = [
        'show_id',
        'title',
        'description',
        'performance_date',
        'ticket_price',
    ];

    // Relation : Le spectacle appartient à une Salle de spectacle
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Show::class, 'show_id');
    }

    // Relation vers vos réservations existantes de billets
    public function bookings(): HasMany
    {
        return $this->hasMany(ShowBooking::class, 'event_id'); // Pensez à ajouter la colonne event_id dans show_bookings
    }
}
