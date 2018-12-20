<?php

namespace Weekendr\Http\Controllers;

use Illuminate\Http\Request;

class MailchimpWebhookController extends Controller
{
    public function store(Request $request)
    {
        dd($request);
    }
}
