<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'phone_no',
        'country_code',
        'email',
        'password',
        'role',
        'address',
        'dob',
        'blood_group',
        'department',
        'post',
        'family_contact_no',
        'photo',
        'resume',
        'remarks',
        'is_verified',
        'is_active',
        'last_login_at',
        'last_read_notices_at',
        'current_device_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'is_verified' => 'boolean',
            'is_active' => 'boolean',
            'last_login_at' => 'datetime',
            'last_read_notices_at' => 'datetime',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEngineer(): bool
    {
        return $this->role === 'engineer';
    }

    public function getProfilePhotoUrlAttribute(): string
    {
        if ($this->photo) {
            if (str_starts_with($this->photo, 'http://') || str_starts_with($this->photo, 'https://')) {
                return $this->photo;
            }
            if (file_exists(public_path($this->photo))) {
                return asset($this->photo);
            }
            if (file_exists(public_path('storage/' . $this->photo)) || Storage::disk('public')->exists($this->photo)) {
                return asset('storage/' . $this->photo);
            }
            return asset($this->photo);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=1E3A5F&color=FFFFFF&bold=true&size=128';
    }

    public function assignedComplaints()
    {
        return $this->hasMany(Complaint::class, 'engineer_id');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'user_id');
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class, 'created_by');
    }

    public function bills()
    {
        return $this->hasMany(Bill::class, 'created_by');
    }

    public function notices()
    {
        return $this->hasMany(Notice::class, 'created_by');
    }
}
