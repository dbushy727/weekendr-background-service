<?php

namespace Weekendr\Console\Commands;

use Illuminate\Console\Command;
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
        $this->usersWithPendingDeals()->each(function ($user) {
            Mailchimp::email($user, $user->flight_deals);
        });
    }

    /**
     * Get users with flight deals attached that he/she has not been notified about yet
     *
     * @return [type] [description]
     */
    public function usersWithPendingDeals()
    {
        return User::with([
            'flight_deals' => function ($q) {
                $q->whereNull('notified_at');
            }])->whereHas('flight_deals', function ($q) {
                return $q->whereNull('notified_at');
            })->get();
    }
}
