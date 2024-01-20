<?php

namespace EmailQ\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class TrackingModel extends Eloquent
{
    protected $table = 'tracking';
    protected $fillable = [
        'email_id',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_content',
        'utm_term',
    ];
}
