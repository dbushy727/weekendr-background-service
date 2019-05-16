<?php

namespace Weekendr\External;

use Crew\Unsplash\HttpClient;
use Crew\Unsplash\Search;

class Unsplash
{
    public function __construct()
    {
        HttpClient::init([
            'applicationId' => env('UNSPLASH_ACCESS_KEY'),
            'utmSource' => 'Weekendr',
        ]);
    }

    public function search($query, $page = 1, $per_page = 10, $orientation = "squarish")
    {
        return Search::photos($query, $page, $per_page, $orientation)->getResults();
    }
}
