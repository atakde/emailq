<?php

namespace EmailQ\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class TemplateModel extends Eloquent
{
    protected $table = 'templates';
    protected $fillable = [
        'name',
        'subject',
        'body',
    ];
}
