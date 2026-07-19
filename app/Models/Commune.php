<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Commune extends Model
{
    protected $table = 'communes';
    protected $primaryKey = 'id_com';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function region(): BelongsTo
    {
        return $this->belongsTo(Region::class, 'id_reg', 'id_reg');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'id_com', 'id_com');
    }
}
