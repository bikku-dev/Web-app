<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CreditCollection extends Model
{
    protected $fillable = [
        'bill_id',
        'amount',
        'collected_by',
        'collected_at',
        'notes',
        'receipt_photo',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'collected_at' => 'datetime',
    ];

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function collectedBy()
    {
        return $this->belongsTo(User::class, 'collected_by');
    }
}
