<?php

namespace Weekendr\Models;

use Illuminate\Database\Eloquent\Model;

class FlightDeal extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'destination_city',
        'departure_origin',
        'departure_destination',
        'departure_carrier',
        'departure_date',
        'return_origin',
        'return_destination',
        'return_carrier',
        'return_date',
        'price',
    ];

    protected $dates = ['departure_date', 'return_date', 'created_at', 'updated_at'];
}
