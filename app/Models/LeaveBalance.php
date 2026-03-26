<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveBalance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'leave_type_id',
        'balance_days',
        'used_days',
        'carry_over_days',
        'year'
    ];

    protected $casts = [
        'balance_days' => 'decimal:2',
        'used_days' => 'decimal:2',
        'carry_over_days' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function leaveType()
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function getAvailableDaysAttribute()
    {
        return ($this->balance_days + $this->carry_over_days) - $this->used_days;
    }

    public function useDays($days)
    {
        $this->used_days += $days;
        $this->save();
    }

    public function returnDays($days)
    {
        $this->used_days -= $days;
        $this->save();
    }

    public static function getBalance($userId, $leaveTypeId, $year = null)
    {
        $year = $year ?? date('Y');
        return self::where('user_id', $userId)
                    ->where('leave_type_id', $leaveTypeId)
                    ->where('year', $year)
                    ->first();
    }

    public static function initializeBalance($userId, $leaveTypeId, $balanceDays = 0, $year = null)
    {
        $year = $year ?? date('Y');
        return self::updateOrCreate(
            ['user_id' => $userId, 'leave_type_id' => $leaveTypeId, 'year' => $year],
            ['balance_days' => $balanceDays]
        );
    }
}
