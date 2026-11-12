<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCatalog extends Model
{
    protected $fillable = [
        'category',
        'name',
        'price',
        'is_active',
    ];
}
