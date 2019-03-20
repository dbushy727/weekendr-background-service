<?php

namespace Weekendr\External;

use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class Skyscanner
{
    protected const BASE_URI = 'https://skyscanner-skyscanner-flight-search-v1.p.rapidapi.com/apiservices/browsequotes/v1.0';
    public const LOCALE      = 'en-US';
    public $request;
    public $response;
    public $data;
    public $results;

    public function __construct(Client $client)
    {
        $this->api_client = $client;
    }

    public function getResults(
        Carbon $outboundDate,
        Carbon $inboundDate,
        string $origin,
        $destination = 'Anywhere',
        $local_country = 'US',
        $local_currency = 'USD'
    ) {
        return $this->createRequest($outboundDate, $inboundDate, $origin, $destination, $local_country, $local_currency)
            ->fetch()
            ->filter(200)
            ->map();
    }

    public function searchPlace($place)
    {
        $url           = sprintf("https://skyscanner-skyscanner-flight-search-v1.p.rapidapi.com/apiservices/autosuggest/v1.0/US/USD/en-US/?query=%s", $place);
        $headers       = ['X-RapidAPI-Key' => env('SKYSCANNER_API_KEY')];
        $this->request = new Request('GET', $url, $headers);

        return collect($this->fetch()->data['Places']);
    }

    protected function createRequest(
        Carbon $outboundDate,
        Carbon $inboundDate,
        string $origin,
        $destination = 'Anywhere',
        $local_country = 'US',
        $local_currency = 'USD'
    ) {
        $url           = sprintf('%s/%s/%s/%s/%s/%s/%s/%s', self::BASE_URI, $local_country, $local_currency, self::LOCALE, $origin, $destination, $outboundDate->toDateString(), $inboundDate->toDateString());
        $headers       = ['X-RapidAPI-Key' => env('SKYSCANNER_API_KEY')];
        $this->request = new Request('GET', $url, $headers);

        return $this;
    }

    protected function fetch()
    {
        if (!$this->request) {
            throw new \Exception('Cannot fetch data. Request is missing.');
        }

        $this->response = $this->api_client->send($this->request);
        $this->data     = json_decode((string) $this->response->getBody(), true);

        return $this;
    }

    protected function getQuotes()
    {
        if (!$this->data) {
            throw new \Exception('Cannot get Quotes. Data is missing.');
        }

        return collect($this->data['Quotes']);
    }

    protected function getPlace($place_id)
    {
        if (!$this->data) {
            throw new \Exception('No data has been set yet.');
        }

        return collect($this->data['Places'])->where('PlaceId', $place_id)->first();
    }

    protected function getCarrier($carrier_id)
    {
        if (!$this->data) {
            throw new \Exception('No data has been set yet.');
        }

        return collect($this->data['Carriers'])->where('CarrierId', $carrier_id)->first();
    }

    protected function filter($price = 200, $direct = true)
    {
        if (!$this->data) {
            throw new \Exception('No data has been set yet.');
        }

        $this->results = collect($this->data['Quotes'])->filter(function ($quote) use ($price, $direct) {
            return $quote['MinPrice'] <= $price && $quote['Direct'] == $direct;
        });

        return $this;
    }

    protected function map()
    {
        return $this->results->map(function ($quote, $key) {
            $quote['OutboundLeg']['Origin'] = $this->getPlace($quote['OutboundLeg']['OriginId']);
            $quote['OutboundLeg']['Destination'] = $this->getPlace($quote['OutboundLeg']['DestinationId']);
            $quote['OutboundLeg']['Carrier'] = $this->getCarrier(array_first($quote['OutboundLeg']['CarrierIds']));

            $quote['InboundLeg']['Origin'] = $this->getPlace($quote['InboundLeg']['OriginId']);
            $quote['InboundLeg']['Destination'] = $this->getPlace($quote['InboundLeg']['DestinationId']);
            $quote['InboundLeg']['Carrier'] = $this->getCarrier(array_first($quote['InboundLeg']['CarrierIds']));

            return $quote;
        });
    }
}
