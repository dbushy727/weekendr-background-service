<?php

namespace Weekendr\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Weekendr\External\Skyscanner;
use Weekendr\Models\FlightDeal;
use Weekendr\Models\User;

class GetFlightDealsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:get-flight-deals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hit the Skyscanner API and get flight deals';

    protected $safeguard = 10;
    protected $error_counter = 1;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $skyscanner = app(Skyscanner::class);

        $this->airports()->each(function ($airport) use ($skyscanner) {
            $this->error_counter = 1;
            $this->createFlightDeals($skyscanner, $airport);
        });
    }

    public function createFlightDeal($deal, $airport)
    {
        $flight_deal = FlightDeal::firstOrCreate([
            'destination_city' => array_get($deal, 'OutboundLeg.Destination.CityName'),
            'departure_origin' => array_get($deal, 'OutboundLeg.Origin.IataCode'),
            'departure_destination' => array_get($deal, 'OutboundLeg.Destination.IataCode'),
            'departure_carrier' => array_get($deal, 'OutboundLeg.Carrier.Name'),
            'departure_date' => Carbon::parse(array_get($deal, 'OutboundLeg.DepartureDate')),
            'return_origin' => array_get($deal, 'InboundLeg.Origin.IataCode'),
            'return_destination' => array_get($deal, 'InboundLeg.Destination.IataCode'),
            'return_carrier' => array_get($deal, 'InboundLeg.Carrier.Name'),
            'return_date' => Carbon::parse(array_get($deal, 'InboundLeg.DepartureDate')),
            'price' => (int) array_get($deal, 'MinPrice') * 100
        ]);

        $this->attachDealToUsers($flight_deal, $airport);
    }

    public function airports()
    {
        return User::select('airport_code')->distinct()->get()->pluck('airport_code');
    }

    public function friday()
    {
        $now = Carbon::now();

        if ($now->isFriday()) {
            return $now;
        }

        return $now->next(5);
    }

    public function sunday()
    {
        return $this->friday()->next(0);
    }

    public function createFlightDeals($skyscanner, $airport)
    {
        try {
            $flight_deals = $skyscanner->getResults($this->friday(), $this->sunday(), $airport);
            foreach ($flight_deals as $deal) {
                $this->createFlightDeal($deal, $airport);
            }
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            // No issues here, just couldnt find deals for this airport
            if ($e->getMessage() == 'No results') {
                return;
            }

            $this->error_counter++;
            if ($this->error_counter >= $this->safeguard) {
                throw new \Exception('Too many errors. Could not create flight deals for airport: ' . $airport);
            }

            $this->createFlightDeals($skyscanner, $airport);
        }
    }

    public function attachDealToUsers($flight_deal, $airport)
    {
        return User::where('airport_code', $airport)->get()->each(function ($user) use ($flight_deal) {
            $user->flight_deals->find($flight_deal->id) ?? $user->flight_deals()->attach($flight_deal->id);
        });
    }
}
