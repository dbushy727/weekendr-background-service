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
        $this->climate->out('Start Notifying Users');

        $this->getUsersWithFlightDeals()->each(function ($users) {
            $this->sendEmail($users);
        });

        $this->climate->out('Finish Notifying Users');
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

    /**
     * Create campaign and send email to users
     *
     * @param  Collection $users
     * @return
     */
    public function sendEmail($users)
    {
        $campaign = $this->createNewCampaign($users);
        $this->setCampaignContent($campaign, $users->first()->flight_deals);

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

    public function createNewCampaign($users)
    {
        $recipients = $this->recipients($users);
        $settings   = $this->settings($users->first()->airport_code, $users->first()->flight_deals);

        return $this->mailchimp_campaigns->addCampaign('regular', $recipients, $settings);
    }

    /**
     * Choose appropriate view based on flight deals and return view content with plugged in data
     *
     * @param  Collection $flight_deals
     * @return string
     */
    public function createView($flight_deals)
    {
        if ($flight_deals->count() > 1) {
            return view('emails.multi-flight-email-2', [
                'flight_deals' => $flight_deals,
                'subject'      => $this->createSubjectLine($flight_deals),
            ])->render();
        }

        return view('emails.single-flight-email-2', [
            'flight_deal' => $flight_deals->first(),
            'subject'     => $this->createSubjectLine($flight_deals),
        ])->render();
    }

    public function setCampaignContent($campaign, $flight_deals)
    {
        $html = $this->createView($flight_deals);

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

    public function multipleFlightDealsSubject($flight_deals)
    {
        $cheapest_price = $flight_deals->sortBy('price')->first()->price / 100;
        if ($flight_deals->unique('departure_date')->count() == 1) {
            $which_weekend  = $flight_deals->first()->isThisWeekend() ? 'this' : 'next';
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
    public function createSubjectLine($flight_deals)
    {
        if ($flight_deals->count() > 1) {
            return $this->multipleFlightDealsSubject($flight_deals);
        }

        return $this->singleFlightDealSubject($flight_deals->first());
    }

    public function settings($airport, $flight_deals)
    {
        return [
            'subject_line' => $this->createSubjectLine($flight_deals),
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
