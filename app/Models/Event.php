<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo; // 🌟 AJOUT SÉCURITÉ
use Illuminate\Database\Eloquent\Relations\HasMany;   // 🌟 AJOUT SÉCURITÉ

class Event extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'show_id',
        'title',
        'description',
        'performance_date'
    ];

    /**
     * Relation : Le spectacle appartient à une Salle de spectacle (table: shows)
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Show::class, 'show_id');
    }

    /**
     * Relation : Un spectacle possède plusieurs catégories de tickets (table: event_tickets)
     */
    public function tickets(): HasMany
    {
        return $this->hasMany(EventTicket::class, 'event_id');
    }

    /**
     * Relation : Un spectacle possède plusieurs réservations de billets (table: show_bookings)
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(ShowBooking::class, 'event_id');
    }
}
