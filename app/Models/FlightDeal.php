<?php

namespace Weekendr\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class FlightDeal extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'status',
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
        'link',
    ];

    protected $dates = ['departure_date', 'return_date', 'created_at', 'updated_at'];

    protected $appends = ['link', 'carriers'];

    public function isThisWeekend()
    {
        return $this->departure_date->isSameDay($this->friday());
    }

    public function getLinkAttribute($value)
    {
        $url = 'https://www.skyscanner.com/transport/flights/%s/%s/%s/%s/?adults=1&children=0&adultsv2=1&childrenv2=&infants=0&cabinclass=economy&rtn=1&preferdirects=true&outboundaltsenabled=false&inboundaltsenabled=false&ref=home#results';

        $replacements = [
            $this->departure_origin,
            $this->departure_destination,
            $this->departure_date->format('ymd'),
            $this->return_date->format('ymd'),
        ];

        return sprintf($url, ...$replacements);
    }

    public function getCarriersAttribute($value)
    {
        return collect([$this->departure_carrier, $this->return_carrier])
            ->unique()
            ->implode('/');
    }

    public function friday()
    {
        $now = Carbon::now();

        if ($now->isFriday()) {
            return $now;
        }

        return $now->next(5);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_flight_deals')->withTimestamps()->withPivot(['notified_at']);
    }

    public function destination()
    {
        return $this->belongsTo(Destination::class, 'destination_city', 'name');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'Approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'Rejected');
    }
}
