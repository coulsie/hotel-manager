<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Show extends Model
{
    protected $fillable = [
        'title',        // Considéré ici comme le Nom de la Salle
        'description',  // Histoire / Détails de la salle
        'total_capacity'
    ];

    // Relation : Une salle a plusieurs spectacles
    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'show_id');
    }
}
