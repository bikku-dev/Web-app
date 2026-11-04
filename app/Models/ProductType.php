<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductType extends Model
{
    protected $fillable = ['name', 'code', 'description', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function getPrefixAttribute(): string
    {
        if (!empty($this->code)) {
            return strtoupper($this->code);
        }
        $name = strtolower($this->name ?? '');
        if (str_contains($name, 'cctv') || str_contains($name, 'camera')) {
            return 'S';
        }
        if (str_contains($name, 'print')) {
            return 'P';
        }
        if (str_contains($name, 'comput') || str_contains($name, 'pc') || str_contains($name, 'laptop')) {
            return 'C';
        }
        return 'O';
    }

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
