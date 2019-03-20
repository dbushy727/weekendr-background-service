<?php

namespace Weekendr\Http\Controllers;

use Weekendr\Models\FlightDeal;
use Weekendr\Repos\FlightDealRepo;

class AdminController
{
    public function index()
    {
        $flight_deals = FlightDeal::pending()->orderByDesc('id')->paginate(50);

        return view('admin', compact('flight_deals'));
    }

    public function sendEmail()
    {
        try {
            app(Weekendr\Console\Commands\NotifyUsersOfDealsCommand::class)->handle();

            return response(200);
        } catch (\Exception $e) {
            return response(500, 'Failed to send email');
        }
    }
}
