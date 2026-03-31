<?php

namespace App\Events;

use App\Models\LeavePlan;
use App\Models\LeaveType;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeavePlanCreated
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public LeavePlan $leavePlan,
        public User $actor,
        public LeaveType $leaveType
    ) {
    }
}

