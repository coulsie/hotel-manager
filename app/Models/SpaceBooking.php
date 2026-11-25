<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SpaceBooking extends Model
{
    use HasFactory;

    protected $table = 'space_bookings';

    protected $fillable = [
        'space_id',
        'company_id',
        'room_id',
        'client_name',
        'client_phone',
        'booking_date',
        'start_time',
        'end_time',
        'pricing_type',
        'attendees',
        'total_amount',
        'amount_paid',
        'payment_status',
        'status',
        'notes',
    ];

    /**
     * Espace réservé (Salle de réunion, séminaire ou coworking).
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(ConferenceSpace::class, 'space_id');
    }

    /**
     * Chambre associée (si imputé sur une note de chambre).
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class, 'room_id');
    }

    /**
     * Reçus de paiement liés à cette réservation.
     */
    public function billings(): HasMany
    {
        return $this->hasMany(Billing::class, 'space_booking_id');
    }
}
