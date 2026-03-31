<?php

namespace App\Events;

use App\Models\LeaveApplication;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveApplicationSubmitted
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public LeaveApplication $leaveApplication,
        public User $user,
        public LeaveType $leaveType,
        public int $requestedDays,
        public int $sickExtraAnnualDeduction = 0
    ) {
    }
}

