<?php

namespace EmailQ\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;

class EmailModel extends Eloquent
{
    protected $table = 'emails';
    protected $fillable = ['to', 'from', 'from_name', 'cc', 'bcc', 'subject', 'body', 'headers', 'status', 'reply_to'];
    protected $casts = [
        'headers' => 'array',
        'attachments' => 'array',
    ];
}
