<?php

namespace Weekendr\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Weekendr\Models\FlightDeal;
use Weekendr\Repos\FlightDealRepo;

class FlightDealsController extends Controller
{
    protected $flight_deals;

    public function __construct(FlightDealRepo $flight_deals)
    {
        $this->flight_deals = $flight_deals;
    }

    public function index()
    {
        return FlightDeal::orderByDesc('id')->paginate(500);
    }

    public function approve($id)
    {
        $flight_deal = FlightDeal::findOrFail($id);

        $flight_deal->update(['status' => 'Approved']);
        $this->flight_deals->attachToUsers($flight_deal);

        return FlightDeal::with(['users'])->findOrFail($id);
    }

    public function reject($id)
    {
        $flight_deal = FlightDeal::findOrFail($id);

        $flight_deal->update(['status' => 'Rejected']);
        $this->flight_deals->removeFromUsers($flight_deal);

        return FlightDeal::findOrFail($id);
    }

    public function approved()
    {
        return FlightDeal::orderByDesc('id')->approved()->paginate(500);
    }

    public function rejected()
    {
        return FlightDeal::orderByDesc('id')
            ->rejected()
            ->whereDate('departure_date', '>=', Carbon::today())
            ->paginate(500);
    }

    public function pending()
    {
        return FlightDeal::orderByDesc('id')->pending()->paginate(500);
    }

    public function ready()
    {
        return $this->flight_deals->approvedFlightsReadyToNotify()->paginate(500);
    }
}
