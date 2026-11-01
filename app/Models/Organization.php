<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    protected $fillable = [
        'name',
        'area_id',
        'city_id',
        'contact_person',
        'contact_no',
        'address',
        'pincode',
        'gst_no',
        'photo',
        'remarks',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'firm_id');
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'firm_id');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'firm_id');
    }
}
