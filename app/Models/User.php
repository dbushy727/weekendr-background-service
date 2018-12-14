<?php

namespace Weekendr\Models;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'airport_code',
    ];

    // protected $touches = ['user_flight_deals'];

    public function flight_deals()
    {
        return $this->belongsToMany(FlightDeal::class, 'user_flight_deals')->withTimestamps()->withPivot(['notified_at']);
    }
}
