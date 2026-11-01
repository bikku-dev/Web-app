<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'firm_id',
        'name',
        'contact_no',
        'department',
        'address',
        'area_id',
        'city_id',
        'remarks',
        'photo',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'firm_id');
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }

    public function bills()
    {
        return $this->hasMany(Bill::class);
    }
}
