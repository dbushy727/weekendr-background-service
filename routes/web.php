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

Route::get('/', 'HomeController@index');
Route::get('/places/{query}', 'HomeController@getPlaces');

Route::post('/mailchimp-webhook', 'MailchimpWebhookController@store');
Route::get('/mailchimp-webhook', 'MailchimpWebhookController@index');
Route::get('/deals/{slug}', 'HomeController@deals');

Route::middleware(['auth.basic'])->group(function () {

    Route::get('/admin', 'AdminController@index');
    Route::post('/admin/send-email', 'AdminController@sendEmail');
    Route::get('/admin/destinations', 'AdminController@destinations');

    Route::get('/flight-deals', 'FlightDealsController@index');
    Route::get('/flight-deals/approved', 'FlightDealsController@approved');
    Route::get('/flight-deals/rejected', 'FlightDealsController@rejected');
    Route::get('/flight-deals/ready', 'FlightDealsController@ready');
    Route::get('/flight-deals/pending', 'FlightDealsController@pending');
    Route::post('/flight-deals/{id}/approve', 'FlightDealsController@approve');
    Route::post('/flight-deals/{id}/reject', 'FlightDealsController@reject');

    Route::get('/destinations', 'DestinationsController@index');
    Route::get('/destinations/pending', 'DestinationsController@pending');
    Route::put('/destinations/{destination}', 'DestinationsController@update');
    Route::get('/destinations/{destination}/images', 'DestinationsController@images');

});


