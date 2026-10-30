<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    // Autorise l'enregistrement en masse de toutes les colonnes (restaurant_table_id, room_id, etc.)
    protected $guarded = [];

    // Relation avec la table / le transat
    public function restaurantTable()
    {
        return $this->belongsTo(RestaurantTable::class);
    }

    // Relation avec la chambre (si imputé sur la chambre)
    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
