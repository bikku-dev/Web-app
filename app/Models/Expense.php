<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = [
        'complaint_id',
        'firm_id',
        'customer_id',
        'created_by',
        'expense_type',
        'amount',
        'details',
        'photo',
        'status',
        'reviewed_by',
        'reviewed_at',
        'admin_remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'reviewed_at' => 'datetime',
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

    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
