<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    // Autorise l'enregistrement en masse de toutes les colonnes dans MariaDB
    protected $guarded = [];

     public function magneticCards()
    {
        return $this->hasMany(MagneticCard::class);
    }

    // Dans app/Models/Room.php

    // Relation avec toutes les commandes imputées sur la chambre
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    // Relation avec toutes les réservations de spectacles imputées sur la chambre
    public function showBookings()
    {
        return $this->hasMany(ShowBooking::class);
    }




}



