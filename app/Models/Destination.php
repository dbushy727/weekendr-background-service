<?php

namespace Weekendr\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Destination extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'image_link',
    ];

    protected $dates = ['created_at', 'updated_at'];
}
