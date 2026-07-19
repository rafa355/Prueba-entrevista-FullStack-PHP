<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Customer extends Model
{
    protected $table = 'customers';

    protected $primaryKey = 'dni';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'dni',
        'id_reg',
        'id_com',
        'email',
        'password',
        'name',
        'last_name',
        'address',
        'date_reg',
        'status',
    ];

    protected $hidden = [
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'date_reg' => 'datetime',
            'status' => 'string',
        ];
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'id_reg', 'id_reg');
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class, 'id_com', 'id_com');
    }
}
