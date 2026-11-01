<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Complaint extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'firm_id',
        'customer_id',
        'product_type_id',
        'service_type_id',
        'engineer_id',
        'issue_description',
        'due_date',
        'photo',
        'status',
        'priority',
        'is_started',
        'started_at',
        'is_resolved',
        'resolved_at',
        'resolution_notes',
        'bill_id',
        'created_by',
        'is_deleted',
    ];

    protected $casts = [
        'due_date' => 'datetime',
        'started_at' => 'datetime',
        'resolved_at' => 'datetime',
        'is_started' => 'boolean',
        'is_resolved' => 'boolean',
        'is_deleted' => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class, 'firm_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function productType()
    {
        return $this->belongsTo(ProductType::class);
    }

    public function serviceType()
    {
        return $this->belongsTo(ServiceType::class);
    }

    public function engineer()
    {
        return $this->belongsTo(User::class, 'engineer_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function bill()
    {
        return $this->belongsTo(Bill::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function isOverdue(): bool
    {
        if (in_array($this->status, ['completed', 'cancelled'])) {
            return false;
        }
        return $this->due_date && $this->due_date->isPast();
    }
}
