<?php

namespace Weekendr\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
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
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        return $this->getUsersWithFlightDeals()->each(function ($users) {
            $this->sendEmail($users);
        });
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
            }])->whereHas('flight_deals', function ($q) {
                $q->whereNull('notified_at');
                $q->whereDate('departure_date', '>=', Carbon::now()->toDateString());
            })->get()->groupBy('airport_code');
    }

    public function sendEmail($users)
    {
        $campaign = $this->createNewCampaign($users);
        $this->setCampaignContent($campaign, $users->first()->flight_deals);

        return $this->send($users, $campaign);
    }

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

    public function createNewCampaign($users)
    {
        $recipients = $this->recipients($users);
        $settings   = $this->settings($users->first()->airport_code);

        return $this->mailchimp_campaigns->addCampaign('regular', $recipients, $settings);
    }

    public function setCampaignContent($campaign, $flight_deals)
    {
        $html = view('emails.campaign', ['flight_deals' => $this->formatDeals($flight_deals)])->render();

        return $this->mailchimp_campaigns->setCampaignContent($campaign->id, ['html' => preg_replace("/\r|\n|\t/", "", $html)]);
    }

    public function recipients($users)
    {
        $list    = $this->getList('Internal List');
        $segment = $this->createSegment($list, $users);

        return [
            'list_id'      => $list->id,
            'segment_opts' => ['saved_segment_id' => $segment->id],
        ];
    }

    public function settings($airport)
    {
        return [
            'subject_line' => 'Weekendr found some deals for this weekend',
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

    /**
     * Display flight deal nicely
     *
     * @param  Collection $flight_deals
     * @return Collection
     */
    public function formatDeals($flight_deals)
    {
        return $flight_deals->map(function ($flight_deal) {
            $carriers = collect([$flight_deal->departure_carrier, $flight_deal->return_carrier])
                ->unique()
                ->implode('/');
            $flight_deal->text = sprintf("($%s) %s on %s", $flight_deal->price / 100, $flight_deal->destination_city, $carriers);
            $flight_deal->link = $this->generateFlightLink($flight_deal);

            return $flight_deal;
        });
    }

    public function generateFlightLink(FlightDeal $flight_deal)
    {
        $url = 'https://www.skyscanner.com/transport/flights/%s/%s/%s/%s/?adults=1&children=0&adultsv2=1&childrenv2=&infants=0&cabinclass=economy&rtn=1&preferdirects=true&outboundaltsenabled=false&inboundaltsenabled=false&ref=home#results';
        $replacements = [
            $flight_deal->departure_origin,
            $flight_deal->departure_destination,
            $flight_deal->departure_date->format('ymd'),
            $flight_deal->return_date->format('ymd')
        ];

        return sprintf($url, ...$replacements);
    }
}
