<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'photo',
        'is_clock_in',
        'is_approved',
        'approved_by',
        'approved_at',
        'admin_remarks',
        'recorded_at',
    ];

    protected $casts = [
        'is_clock_in' => 'boolean',
        'is_approved' => 'boolean',
        'approved_at' => 'datetime',
        'recorded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
