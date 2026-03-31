<?php

namespace App\Events;

use App\Models\LeavePlan;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LeavePlanStatusChanged
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public LeavePlan $leavePlan,
        public User $actor,
        public string $status,
        public ?string $rejectionReason = null
    ) {
    }
}

