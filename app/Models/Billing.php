<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Billing extends Model
{
    use HasFactory;

    /**
     * Les attributs assignables en masse.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'receipt_number',
        'room_id',
        'order_id',
        'event_ticket_id',
        'service_type',
        'amount_paid',
        'payment_method',
        'status'
    ];

    /**
     * Générateur automatique de numéro de reçu unique (Format local Ivoirien : REC-AAAAMMJJ-0001)
     * Se base sur la date du jour et incrémente une séquence de 4 chiffres.
     *
     * @return string
     */
    public static function generateReceiptNumber(): string
    {
        $dateStr = now()->format('Ymd');
        $latest = self::where('receipt_number', 'like', "REC-{$dateStr}-%")->latest('id')->first();

        $sequence = $latest ? intval(substr($latest->receipt_number, -4)) + 1 : 1;

        return "REC-" . $dateStr . "-" . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Relation inverse : Un paiement peut être associé à une chambre (Hébergement / Checkout).
     */
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    /**
     * Relation inverse : Un paiement peut être associé à une commande (Restaurant / Bar).
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relation inverse : Un paiement peut être associé à un ticket de spectacle (Événementiel).
     */
    public function eventTicket(): BelongsTo
    {
        return $this->belongsTo(EventTicket::class);
    }
}
