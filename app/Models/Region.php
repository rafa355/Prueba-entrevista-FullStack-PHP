<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Region extends Model
{
    protected $table = 'regions';

    protected $primaryKey = 'id_reg';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'status' => 'string',
        ];
    }

    public function communes(): HasMany
    {
        return $this->hasMany(Commune::class, 'id_reg', 'id_reg');
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class, 'id_reg', 'id_reg');
    }
}
