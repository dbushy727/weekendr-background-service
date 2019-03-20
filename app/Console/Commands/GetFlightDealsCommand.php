<?php

namespace Weekendr\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use League\CLImate\CLImate;
use Validator;
use Weekendr\External\Skyscanner;
use Weekendr\External\Slack;
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
    protected $deals_counter = 0;
    protected $slack;
    protected $climate;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(Slack $slack, CLImate $climate)
    {
        parent::__construct();

        $this->slack = $slack;
        $this->climate = $climate;
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

            try {
                $this->createFlightDeals($skyscanner, $airport);
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
            }
        });

        if ($this->deals_counter > 0) {
            $this->slack->notify();
        }

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
            $this->climate->green("Found a flight deal for {$airport} ". array_get($deal, 'OutboundLeg.Origin.IataCode') . ' ' .  array_get($deal, 'OutboundLeg.Destination.IataCode'));
            $this->deals_counter++;

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

    public function failsFlightDealValidation($deal)
    {
        $validator = Validator::make($deal, [
            'OutboundLeg.Origin.IataCode'      => 'required',
            'OutboundLeg.Destination.IataCode' => 'required',
            'OutboundLeg.DepartureDate'        => 'required',
            'InboundLeg.DepartureDate'         => 'required',
            'OutboundLeg.Destination.CityName' => 'required',
            'OutboundLeg.Carrier.Name'         => 'required',
            'InboundLeg.Origin.IataCode'       => 'required',
            'InboundLeg.Destination.IataCode'  => 'required',
            'InboundLeg.Carrier.Name'          => 'required',
            'MinPrice'                         => 'required',
        ]);

        return $validator->fails();
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
                if ($this->failsFlightDealValidation($deal)) {
                    continue;
                }

                $flight_deal = $this->createFlightDeal($deal, $airport);
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
}
