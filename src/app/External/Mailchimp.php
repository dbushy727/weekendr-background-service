<?php

namespace Weekendr\External;

use Weekendr\Models\User;

class Mailchimp
{
    public static function email(User $user, $flight_deals)
    {
        \Log::info('******===+++===******');
        \Log::info('Sending email to: ' . $user->email);
        \Log::info('Here are the flight deals:');
        $flight_deals->each(function ($flight_deal) {
            $notification = sprintf("$%s: %s - %s  %s", $flight_deal->price  / 100, $flight_deal->departure_date->format('m/d/Y'), $flight_deal->return_date->format('m/d/Y'), $flight_deal->destination_city);
            \Log::info($notification);
        });
    }
}
