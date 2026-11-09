<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShowBooking extends Model
{
    /**
     * Les attributs qui peuvent être assignés en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'event_id',      // 🌟 AJOUT SÉCURITÉ
        'show_id',
        'user_id',
        'customer_name', // 🌟 AJOUT SÉCURITÉ (Saisie manuelle)
        'room_id',
        'status',
        'seat_number',
        'ticket_type',
        'amount_paid',
    ];

    /**
     * Relation : La réservation appartient à un concert/événement (table: events)
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    /**
     * Relation : La réservation appartient historiquement à une salle (table: shows)
     */
    public function venue(): BelongsTo
    {
        return $this->belongsTo(Show::class, 'show_id');
    }
}
