<?php

namespace Weekendr\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use League\CLImate\CLImate;
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

    protected $safeguard = 30;
    protected $error_counter = 1;
    protected $climate;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->climate = new CLImate;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->climate->out(Carbon::now()->toDatetimeString() . 'Start Getting Flights');
        $skyscanner = app(Skyscanner::class);

        $this->airports()->each(function ($airport) use ($skyscanner) {
            $now = Carbon::now()->toDatetimeString();
            $this->error_counter = 1;

            $this->climate->out("[{$now}] Fetching deals for airport: {$airport}");
            $this->createFlightDeals($skyscanner, $airport);
        });

        $this->climate->green(Carbon::now()->toDatetimeString() . 'Finished Getting Flights');
    }

    public function createFlightDeal($deal, $airport)
    {
        return FlightDeal::where([
            'departure_origin'      => array_get($deal, 'OutboundLeg.Origin.IataCode'),
            'departure_destination' => array_get($deal, 'OutboundLeg.Destination.IataCode'),
            'departure_date'        => Carbon::parse(array_get($deal, 'OutboundLeg.DepartureDate')),
            'return_date'           => Carbon::parse(array_get($deal, 'InboundLeg.DepartureDate')),
        ])->firstOr(function () use ($deal, $airport) {
            $this->climate->green("Found a flight deal for {$airport}");
            app('sentry')->captureMessage("Found a flight deal for {$airport}");
            return FlightDeal::create([
                'departure_origin'      => array_get($deal, 'OutboundLeg.Origin.IataCode'),
                'departure_destination' => array_get($deal, 'OutboundLeg.Destination.IataCode'),
                'departure_date'        => Carbon::parse(array_get($deal, 'OutboundLeg.DepartureDate')),
                'return_date'           => Carbon::parse(array_get($deal, 'InboundLeg.DepartureDate')),
                'destination_city'      => array_get($deal, 'OutboundLeg.Destination.CityName'),
                'departure_carrier'     => array_get($deal, 'OutboundLeg.Carrier.Name'),
                'return_origin'         => array_get($deal, 'InboundLeg.Origin.IataCode'),
                'return_destination'    => array_get($deal, 'InboundLeg.Destination.IataCode'),
                'return_carrier'        => array_get($deal, 'InboundLeg.Carrier.Name'),
                'price'                 => (int) array_get($deal, 'MinPrice') * 100,
            ]);
        });
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
        // For some reason US-sky brings more results for US than Anywhere
        try {
            $flight_deals_us         = $skyscanner->getResults($this->friday(), $this->sunday(), $airport, 'US-sky');
            $flight_deals_us2        = $skyscanner->getResults($this->friday()->next(5), $this->sunday()->next(0), $airport, 'US-sky');
            $flight_deals_worldwide  = $skyscanner->getResults($this->friday(), $this->sunday(), $airport, 'Anywhere');
            $flight_deals_worldwide2 = $skyscanner->getResults($this->friday()->next(5), $this->sunday()->next(0), $airport, 'Anywhere');

            $deals = $flight_deals_us->merge($flight_deals_us2)->merge($flight_deals_worldwide)->merge($flight_deals_worldwide2);

            foreach ($deals as $deal) {
                $flight_deal = $this->createFlightDeal($deal, $airport);
                $this->attachDealToUsers($flight_deal, $airport);
            }
        } catch (\Exception $e) {
            // No issues here, just couldnt find deals for this airport
            if ($e->getMessage() == 'No results') {
                return;
            }

            $this->error_counter++;
            $this->climate->red("Error: {$e->getMessage()}");

            if ($this->error_counter >= $this->safeguard) {
                throw new \Exception('Too many errors. Could not create flight deals for airport: ' . $airport);
            }

            $this->tryAgain($skyscanner, $airport);
        }
    }

    public function tryAgain($skyscanner, $airport)
    {
        sleep(2);
        return $this->createFlightDeals($skyscanner, $airport);
    }

    public function attachDealToUsers($flight_deal, $airport)
    {
        return User::where('airport_code', $airport)->get()->each(function ($user) use ($flight_deal) {
            $user->flight_deals->find($flight_deal->id) ?? $user->flight_deals()->attach($flight_deal->id);
        });
    }
}
