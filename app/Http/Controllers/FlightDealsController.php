<?php

namespace Weekendr\Http\Controllers;

use Illuminate\Http\Request;
use Weekendr\Models\FlightDeal;

class FlightDealsController extends Controller
{
    public function index()
    {
        return FlightDeal::orderByDesc('id')->paginate(50);
    }

    public function approve($id)
    {
        FlightDeal::findOrFail($id)->update(['approved' => true]);

        return FlightDeal::findOrFail($id);
    }
}
