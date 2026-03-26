<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'start_date',
        'end_date',
        'reason',
        'status',
        'admin_remarks',
        'admin_action_date',
        'approved_by',
        'is_read_by_admin',
        'total_days',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'admin_action_date' => 'datetime',
        'is_read_by_admin' => 'boolean',
        'total_days' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function documents()
    {
        return $this->hasMany(LeaveApplicationDocument::class);
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeUnreadByAdmin($query)
    {
        return $query->where('is_read_by_admin', false);
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($leaveApplication) {
            $start = \Carbon\Carbon::parse($leaveApplication->start_date);
            $end = \Carbon\Carbon::parse($leaveApplication->end_date);
            $leaveApplication->total_days = $start->diffInDays($end) + 1;
        });
    }
}
