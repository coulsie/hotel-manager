<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraService extends Model
{
    protected $fillable = [
        'category',
        'service_name',
        'quantity',
        'unit_price',
        'total_amount',
        'room_id',
        'customer_name',
        'work_status',
        'payment_status',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
