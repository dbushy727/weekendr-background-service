<?php

namespace Weekendr\Http\Controllers;

use Illuminate\Support\Facades\Artisan;
use Weekendr\Console\Commands\NotifyUsersOfDealsCommand;
use Weekendr\Models\Destination;
use Weekendr\Models\FlightDeal;
use Weekendr\Repos\FlightDealRepo;

class AdminController
{
    protected $flight_deals;

    public function __construct(FlightDealRepo $flight_deals)
    {
        $this->flight_deals = $flight_deals;
    }

    public function index()
    {
        return view('backend.admin');
    }

    public function sendEmail()
    {
        try {
            $results = Artisan::call('custom:notify-users', ['interface' => 'web']);

            return response(200);
        } catch (\Exception $e) {
            return response("Failed to send email \n" . $e->getMessage(), 500);
        }
    }

    public function destinations()
    {
        return view('backend.destinations');
    }
}
