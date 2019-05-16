@extends('layouts.frontend-layout')

@section('frontend-content')
    <header>

        <h1>Weekendr</h1>
        <h3>{{ $flight_deals->count() }} Deals for {{ $airport }}</h3>

        <div class="flight-deals flex-cards">
            @foreach($flight_deals->sortBy('created_at') as $flight_deal)
            <div class="flight-deal card">
                <div class="card-header">
                    <div class="destination">{{ $flight_deal->destination_city }}</div>
                    <div class="price">${{ $flight_deal->price / 100 }}</div>
                </div>
                <div class="card-body">
                    <div class="flight-legs">
                        <div class="outbound-flight">
                            <div class="airport">{{ $flight_deal->departure_origin }}</div>
                            <div class="lines">---</div>
                            <div class="route">
                                <div class="flight-date">{{ $flight_deal->departure_date->format('M d')}}</div>
                                <img src="/images/flying-airplane.svg" class="flight-icon" alt="">
                                <div class="carrier">{{ $flight_deal->departure_carrier }}</div>
                            </div>
                            <div class="lines">---</div>
                            <div class="airport">{{ $flight_deal->departure_destination}}</div>
                        </div>
                        <div class="return-flight">
                            <div class="airport">{{ $flight_deal->return_origin }}</div>
                            <div class="lines">---</div>
                            <div class="route">
                                <div class="flight-date">{{ $flight_deal->return_date->format('M d')}}</div>
                                <img src="/images/flying-airplane.svg" class="flight-icon" alt="">
                                <div class="carrier">{{ $flight_deal->return_carrier }}</div>
                            </div>
                            <div class="lines">---</div>
                            <div class="airport">{{ $flight_deal->return_destination }}</div>
                        </div>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="dates">{{ $flight_deal->departure_date->format('M d') }} - {{ $flight_deal->return_date->format('M d') }}</div>
                    <div class="action">
                        <a class="button" href="{{$flight_deal->link}}" target="_BLANK">View Flight <i class="fas fa-arrow-circle-right"></i></a>
                        <div class="date-found">Deal Found: {{ $flight_deal->created_at->tz('America/New_York')->format('M d h:i') }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </header>
