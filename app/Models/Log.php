<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';
    public $timestamps = false;

    protected $fillable = [
        'method',
        'url',
        'ip',
        'request_body',
        'response_status',
        'response_body',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'response_status' => 'integer',
            'created_at' => 'datetime',
        ];
    }
}
