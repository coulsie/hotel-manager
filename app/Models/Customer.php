<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'full_name',
        'phone',
        'email',
        'nationality',
        'id_type',
        'id_card_number', // Votre colonne existante conservée
        'address',
        'notes',
    ];

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }
}
