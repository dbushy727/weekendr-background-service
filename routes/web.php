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

Route::get('/admin', 'AdminController@index');

Route::get('/flight-deals', 'FlightDealsController@index');
Route::get('/flight-deals/approved', 'FlightDealsController@approved');
Route::get('/flight-deals/unapproved', 'FlightDealsController@unapproved');
Route::post('/flight-deals/{id}/approve', 'FlightDealsController@approve');

Route::post('/mailchimp-webhook', 'MailchimpWebhookController@store');

Route::get('/mailchimp-webhook', 'MailchimpWebhookController@index');