<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomAccessLog extends Model
{
    // Autorise l'enregistrement en masse
    protected $guarded = [];

    // Désactive les colonnes automatiques created_at et updated_at pour cette table
    public $timestamps = false;

    // Déclaration optionnelle pour lier les relations dans le futur
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function magneticCard()
    {
        return $this->belongsTo(MagneticCard::class);
    }
}
