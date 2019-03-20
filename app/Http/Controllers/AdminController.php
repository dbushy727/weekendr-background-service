<?php

namespace Weekendr\Http\Controllers;

use Weekendr\Models\FlightDeal;
use Weekendr\Repos\FlightDealRepo;

class AdminController
{
    public function index()
    {
        $flight_deals = FlightDeal::orderByDesc('id')->paginate(50);

        return view('admin', compact('flight_deals'));
    }
}
