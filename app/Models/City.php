<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['area_id', 'name', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
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
