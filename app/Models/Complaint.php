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

    public static function generateTicketNumber($productTypeId = null): string
    {
        $prefix = 'O';
        if ($productTypeId) {
            $product = ProductType::find($productTypeId);
            if ($product) {
                $prefix = $product->prefix;
            }
        }

        // 2-digit Year (e.g. '26') and 2-digit Month (e.g. '11' for Nov, '12' for Dec)
        $yearMonth = date('ym');
        $basePrefix = $prefix . $yearMonth;

        // Find existing complaints starting with basePrefix in the current month
        $latest = static::withTrashed()
            ->where('ticket_number', 'LIKE', "{$basePrefix}%")
            ->orderByRaw("CAST(SUBSTRING(ticket_number, " . (strlen($basePrefix) + 1) . ") AS UNSIGNED) DESC")
            ->first();

        $seq = 1;
        if ($latest && preg_match('/^' . preg_quote($basePrefix, '/') . '(\d+)$/', $latest->ticket_number, $matches)) {
            $seq = ((int)$matches[1]) + 1;
        }

        return $basePrefix . str_pad($seq, 3, '0', STR_PAD_LEFT);
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
