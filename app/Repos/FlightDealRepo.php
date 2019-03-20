<?php

namespace Weekendr\Repos;

use Weekendr\External\Skyscanner;
use Weekendr\Models\FlightDeal;
use Weekendr\Models\User;

class FlightDealRepo
{
    protected $flight_deals;

    public function __construct(FlightDeal $flight_deals)
    {
        $this->flight_deals = $flight_deals;
    }

    public function attachToUsers(FlightDeal $flight_deal)
    {
        $airport       = sprintf("%s-sky", $flight_deal->departure_origin);
        $region        = app('Weekendr\External\Skyscanner')->searchPlace($flight_deal->departure_origin)->first()['CityId'];
        $airport_codes = collect([$airport, $region])->unique();

        return User::whereIn('airport_code', $airport_codes)->get()->map(function ($user) use ($flight_deal) {
            $user->flight_deals->find($flight_deal->id) ?? $user->flight_deals()->attach($flight_deal->id);
        });
    }

    public function approve(FlightDeal $flight_deal)
    {
        return $flight_deal->update(['approved' => 1]);
    }
}
