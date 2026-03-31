<?php

namespace App\Events;

use App\Models\LeaveApplication;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeaveApplicationStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public LeaveApplication $leaveApplication,
        public User $actor,
        public string $status,
        public ?string $remarks = null
    ) {
    }
}

