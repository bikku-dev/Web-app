<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $fillable = ['name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function cities()
    {
        return $this->hasMany(City::class);
    }

    public function organizations()
    {
        return $this->hasMany(Organization::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }
}
