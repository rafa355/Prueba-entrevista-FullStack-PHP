<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    protected $table = 'tokens';

    public $timestamps = false;

    protected $fillable = [
        'token',
        'email',
        'date_reg',
        'ttl',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date_reg' => 'datetime',
            'ttl' => 'integer',
            'status' => 'string',
        ];
    }

    public function isExpired(): bool
    {
        return $this->date_reg->addMinutes($this->ttl)->isPast();
    }
}
