<?php

namespace Weekendr\Http\Controllers;

use Illuminate\Http\Request;
use Weekendr\External\Unsplash;
use Weekendr\Models\Destination;

class DestinationsController extends Controller
{
    public function index()
    {
        return Destination::all();
    }

    public function pending()
    {
        return Destination::whereNull('image_link')->get();
    }

    public function images($destination)
    {
        return app(Unsplash::class)->search($destination);
    }

    public function update($destination, Request $request)
    {
        $destination = Destination::where('name', $destination)->firstOrFail();

        $destination->update(['image_link' => $request->input('image_url')]);

        return Destination::findOrFail($destination->id);
    }
}
