<?php

namespace Weekendr\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Mailchimp\MailchimpCampaigns;
use Mailchimp\MailchimpLists;
use Weekendr\External\Mailchimp;
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
        return $this->getFlightDeals()->each(function ($data, $airport_code) {
            return $this->sendEmail($airport_code, $data->first()->flight_deals);
        });
    }

    /**
     * Get upcoming flight deals grouped by airport
     *
     * @return Collection
     */
    public function getFlightDeals()
    {
        $users =  User::with([
            'flight_deals' => function ($q) {
                $q->whereNull('notified_at');
            }])->whereHas('flight_deals', function ($q) {
                return $q->whereNull('notified_at');
            })->get();

        return $users->groupBy('airport_code')->take(2);
    }

    public function mailchimp_campaigns()
    {
        return new MailchimpCampaigns(env('MAILCHIMP_API_KEY'));
    }

    public function mailchimp_lists()
    {
        return new MailchimpLists(env('MAILCHIMP_API_KEY'));
    }

    public function getList($list_name)
    {
        return collect($this->mailchimp_lists()->getLists()->lists)->where('name', $list_name)->first();
    }

    public function recipients($airport)
    {
        return [
            'list_id'      => $this->getList('Internal List')->id,
            'segment_opts' => [
                'match'      => 'all',
                'conditions' => [
                    [
                        'condition_type' => 'TextMerge',
                        'op'             => 'is',
                        'field'          => 'MERGE5',
                        'value'          => $airport,
                    ]
                ],
            ],
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

    public function createNewCampaign($mailchimp_campaigns, $airport)
    {
        return $mailchimp_campaigns->addCampaign('regular', $this->recipients($airport), $this->settings($airport));
    }

    public function setCampaignContent($mailchimp_campaigns, $campaign, $flight_deals)
    {
        $html = view('emails.campaign', ['flight_deals' => $this->formatDeals($flight_deals)])->render();

        return $mailchimp_campaigns->setCampaignContent($campaign->id, ['html' => preg_replace("/\r|\n|\t/", "", $html)]);
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
            // ($170) Atlanta on American Airlines/JetBlue
            $carriers = collect([$flight_deal->departure_carrier, $flight_deal->return_carrier])->unique()->implode('/');

            return sprintf("($%s) %s on %s", $flight_deal->price / 100, $flight_deal->destination_city, $carriers);
        });
    }

    public function sendEmail($airport, $flight_deals)
    {
        $mailchimp_campaigns = $this->mailchimp_campaigns();
        $campaign            = $this->createNewCampaign($mailchimp_campaigns, $airport);
        $this->setCampaignContent($mailchimp_campaigns, $campaign, $flight_deals);

        return $mailchimp_campaigns->send($campaign->id);
    }
}
