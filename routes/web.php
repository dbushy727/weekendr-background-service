<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/flight-deals', 'FlightDealsController@index');
Route::post('/flight-deals/{id}/approve', 'FlightDealsController@approve');

Route::post('/mailchimp-webhook', 'MailchimpWebhookController@store');

Route::get('/mailchimp-webhook', 'MailchimpWebhookController@index');