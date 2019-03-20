<?php

namespace Weekendr\External;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Slack
{
    protected $api_client;
    protected const BASE_URI = 'https://hooks.slack.com/services/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';

    public function __construct(Client $client)
    {
        $this->api_client = $client;
    }

    public function body()
    {
        $link = "http://localhost:8000/admin";

        return '{"text": "We found some flight deals", "attachments": [{"fallback": "Go to Admin Dashboard ' . $link .'", "actions": [{"type": "button", "text": "Check em out 🛫", "url": "'. $link . '", "style": "primary"} ] } ] }';
    }

    public function notify()
    {
        $headers  = ['cache-control' => 'no-cache', 'Content-Type' => 'application/json'];
        $request  = new Request('POST', self::BASE_URI, $headers, $this->body());
        $response = $this->api_client->send($request);

        return json_decode((string) $response->getBody(), true);
    }
}
