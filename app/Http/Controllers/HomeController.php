<?php

namespace Weekendr\Http\Controllers;

use Weekendr\Models\FlightDeal;
use Weekendr\Models\User;
use Weekendr\Repos\FlightDealRepo;

class HomeController
{
    protected $flight_deals;

    public function __construct(FlightDealRepo $flight_deals)
    {
        $this->flight_deals = $flight_deals;
    }

    public function index()
    {
        return view('frontend.home');
    }

    protected function dealsByAirport($places, $airport_code)
    {
        $place        = $places->where('PlaceId', $airport_code)->first();
        $airport      = str_replace('-sky', '', $place['PlaceId']);
        $flight_deals = $this->flight_deals->upcomingFlightDeals($airport)->take(10);

        return view('frontend.deals', ['flight_deals' => $flight_deals, 'airport' => $place['PlaceName']]);
    }

    protected function dealsByRegion($places, $airport_code)
    {
        $flight_deals = $places->where('CityId', $airport_code)->reduce(function ($deals, $place) {
            if (!$deals) {
                $deals = collect();
            }

            $upcoming = $this->flight_deals->upcomingFlightDeals(str_replace('-sky', '', $place['PlaceId']));

            return $deals->merge($upcoming);
        })->take(10);

        $airport = $places->where('PlaceId', $airport_code)->first()['PlaceName'];
        return view('frontend.deals', compact('flight_deals', 'airport'));
    }

    public function deals($airport_code)
    {
        $places  = app('Weekendr\External\Skyscanner')->searchPlace($airport_code);

        if (!$places->count()) {
            return abort(404);
        }

        // If getting deals by airport, not region
        if ($places->first()['CityId'] != $airport_code) {
            return $this->dealsByAirport($places, $airport_code);
        }

        return $this->dealsByRegion($places, $airport_code);
    }

    public function getPlaces($query)
    {
        $curl    = curl_init();
        $api_key = env('SKYSCANNER_API_KEY');

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://skyscanner-skyscanner-flight-search-v1.p.rapidapi.com/apiservices/autosuggest/v1.0/US/USD/en-US/?query=" . urlencode($query),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_POSTFIELDS => "",
            CURLOPT_HTTPHEADER => array(
                "X-RapidAPI-Key: {$api_key}",
                "cache-control: no-cache"
            ),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return abort(500, $err);
        } else {
            return $response;
        }
    }
}
