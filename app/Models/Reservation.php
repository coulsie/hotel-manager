<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    protected $fillable = [
        'customer_id', 'room_id', 'check_in_date', 'check_out_date',
        'nights', 'total_amount', 'deposit_paid', 'status',
        'provenance', 'destination', 'special_requests'
    ];

    protected $casts = [
        'check_in_date'  => 'date',
        'check_out_date' => 'date',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
