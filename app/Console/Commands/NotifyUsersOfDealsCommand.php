<?php

namespace Weekendr\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use League\CLImate\CLImate;
use Mailchimp\MailchimpCampaigns;
use Mailchimp\MailchimpLists;
use Weekendr\External\Mailchimp;
use Weekendr\Models\FlightDeal;
use Weekendr\Models\User;

class NotifyUsersOfDealsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:notify-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify users of upcoming deals';

    protected $mailchimp_lists;

    protected $mailchimp_campaigns;

    protected $climate;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->mailchimp_campaigns = new MailchimpCampaigns(env('MAILCHIMP_API_KEY'));
        $this->mailchimp_lists     = new MailchimpLists(env('MAILCHIMP_API_KEY'));
        $this->climate             = new CLImate;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->climate->out(Carbon::now()->toDatetimeString() . ' Start Notifying Users');

        $this->getUsersWithFlightDeals()->each(function ($users) {
            $users->first()
                ->flight_deals
                ->groupBy('destination_city')
                ->chunk(5)
                ->each(function ($destinations) use ($users) {
                    $this->sendEmail($users, $destinations);
                });
        });

        $this->climate->out(Carbon::now()->toDatetimeString() . ' Finish Notifying Users');
    }

    /**
     * Get upcoming flight deals grouped by airport
     *
     * @return Collection
     */
    public function getUsersWithFlightDeals()
    {
        return User::with([
            'flight_deals' => function ($q) {
                $q->whereNull('notified_at');
                $q->whereDate('departure_date', '>=', Carbon::now()->toDateString());
                $q->where('flight_deals.created_at', '>=', Carbon::now()->subMinute(30));
            }])->whereHas('flight_deals', function ($q) {
                $q->whereNull('notified_at');
                $q->whereDate('departure_date', '>=', Carbon::now()->toDateString());
                $q->where('flight_deals.created_at', '>=', Carbon::now()->subMinute(30));
            })->get()->groupBy('airport_code');
    }

    /**
     * Create campaign and send email to users
     *
     * @param  Collection $users
     * @return
     */
    public function sendEmail($users, $destinations)
    {
        $campaign = $this->createNewCampaign($users, $destinations);
        $this->setCampaignContent($campaign, $destinations);

        return $this->send($users, $campaign);
    }

    /**
     * Send mail campaign
     *
     * @param  Collection $users
     * @param  [type] $campaign [description]
     * @return [type]           [description]
     */
    public function send($users, $campaign)
    {
        $this->mailchimp_campaigns->send($campaign->id);
        $this->updateUserNotificationTime($users);

        return true;
    }

    public function updateUserNotificationTime($users)
    {
        $now = Carbon::now();
        $users->each(function ($user) use ($now) {
            $user->flight_deals->each(function ($flight_deal) use ($now) {
                $flight_deal->pivot->notified_at = $now;
                $flight_deal->pivot->save();
            });
        });
    }

    public function createNewCampaign($users, $destinations)
    {
        $recipients = $this->recipients($users);
        $settings   = $this->settings($users->first()->airport_code, $destinations);

        return $this->mailchimp_campaigns->addCampaign('regular', $recipients, $settings);
    }

    /**
     * Choose appropriate view based on flight deals and return view content with plugged in data
     *
     * @param  Collection $flight_deals
     * @return string
     */
    public function createView($destinations)
    {
        // Only one destination and that destination only has one flight deal
        if ($destinations->count() == 1 && $destinations->first()->count() == 1) {
            return view('emails.single-flight-email-minify', [
                'flight_deal' => $destinations->first()->first(),
                'subject'     => $this->createSubjectLine($destinations),
            ])->render();
        }

        return view('emails.multi-flight-email-minify', [
            'destinations'     => $destinations,
            'all_flight_deals' => $this->allFlightDealsFromDestinations($destinations),
            'subject'          => $this->createSubjectLine($destinations),
        ])->render();
    }

    public function setCampaignContent($campaign, $destinations)
    {
        $html = $this->createView($destinations);

        return $this->mailchimp_campaigns->setCampaignContent($campaign->id, ['html' => preg_replace("/\r|\n|\t/", "", $html)]);
    }

    public function recipients($users)
    {
        $list    = $this->getList(env('MAILCHIMP_LIST'));
        $segment = $this->createSegment($list, $users);

        return [
            'list_id'      => $list->id,
            'segment_opts' => ['saved_segment_id' => $segment->id],
        ];
    }

    public function allFlightDealsFromDestinations($destinations)
    {
        return $destinations->flatMap(function ($flight_deals, $destination) {
            return $flight_deals;
        });
    }
    public function getCheapestFlightPrice($destinations)
    {
        return $this->allFlightDealsFromDestinations($destinations)->sortBy('price')->first()->price / 100;
    }

    public function multipleFlightDealsSubject($destinations)
    {
        $cheapest_price = $this->getCheapestFlightPrice($destinations);
        $all_flight_deals = $this->allFlightDealsFromDestinations($destinations);

        if ($all_flight_deals->unique('departure_date')->count() == 1) {
            $which_weekend  = $all_flight_deals->first()->isThisWeekend() ? 'this' : 'next';
            return sprintf("We found flights as low as $%s for %s weekend", $cheapest_price, $which_weekend);
        }

        return sprintf("We found some flights starting at $%s for the next two weekends", $cheapest_price);
    }

    /**
     * Construct subject line for a single flight deal
     *
     * @param  FlightDeal $flight_deal
     * @return string
     */
    public function singleFlightDealSubject($flight_deal)
    {
        $price = $flight_deal->price / 100;
        $which_weekend = $flight_deal->isThisWeekend() ? 'this upcoming' : 'next';

        return sprintf("($%s) %s for %s weekend", $price, $flight_deal->destination_city, $which_weekend);
    }

    /**
     * Create a subject line based on the flight deals presented
     * @param  Collection $flight_deals
     * @return string
     */
    public function createSubjectLine($destinations)
    {
        // Only one destination and that destination only has one flight deal
        if ($destinations->count() == 1 && $destinations->first()->count() == 1) {
            return $this->singleFlightDealSubject($destinations->first()->first());
        }

        return $this->multipleFlightDealsSubject($destinations);
    }

    public function settings($airport, $destinations)
    {
        return [
            'subject_line' => $this->createSubjectLine($destinations),
            'title'        => sprintf("[%s] %s", Carbon::now()->toDatetimeString(), $airport),
            'from_name'    => 'Weekendr',
            'reply_to'     => 'no-reply@weekendr.io',
        ];
    }

    public function getList($list_name)
    {
        return collect($this->mailchimp_lists->getLists()->lists)
            ->where('name', $list_name)
            ->first();
    }

    public function createSegment($list, $users)
    {
        $name = sprintf("[%s] %s", Carbon::now()->toDatetimeString(), $users->first()->airport_code);

        return $this->mailchimp_lists->addSegment($list->id, $name, [
            'static_segment' => $users->pluck('email')->toArray(),
        ]);
    }
}
