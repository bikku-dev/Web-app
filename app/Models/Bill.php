<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bill extends Model
{
    protected $fillable = [
        'bill_number',
        'complaint_id',
        'firm_id',
        'customer_id',
        'created_by',
        'amount',
        'bill_type',
        'payment_status',
        'is_handed',
        'handed_over_at',
        'handed_over_to',
        'handover_remarks',
        'gst_bill_number',
        'photo',
        'asset_photo',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'is_handed' => 'boolean',
        'handed_over_at' => 'datetime',
    ];

    public function complaint()
    {
        return $this->belongsTo(Complaint::class);
    }

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'firm_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function handedOverTo()
    {
        return $this->belongsTo(User::class, 'handed_over_to');
    }

    public function creditCollections()
    {
        return $this->hasMany(CreditCollection::class);
    }
}
