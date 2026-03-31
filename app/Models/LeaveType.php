<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'max_days_per_year',
        'requires_approval',
        'requires_documentation',
        'paid',
        'carry_over_allowed',
        'max_carry_over_days',
        'accrual_frequency',
        'probation_restriction',
        'is_active',
    ];

    protected $casts = [
        'requires_approval' => 'boolean',
        'requires_documentation' => 'boolean',
        'paid' => 'boolean',
        'carry_over_allowed' => 'boolean',
        'probation_restriction' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function leaveApplications()
    {
        return $this->hasMany(LeaveApplication::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
