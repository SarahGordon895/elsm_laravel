<?php

namespace App\Listeners;

use App\Events\LeavePlanStatusChanged;
use App\Models\SystemNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleLeavePlanStatusNotifications implements ShouldQueue
{
    public function handle(LeavePlanStatusChanged $event): void
    {
        if ($event->status === 'approved') {
            SystemNotification::sendLeavePlanNotification(
                $event->leavePlan->user_id,
                'approved',
                ['leave_plan_id' => $event->leavePlan->id],
                true,
                true
            );
            return;
        }

        if ($event->status === 'rejected') {
            SystemNotification::sendLeavePlanNotification(
                $event->leavePlan->user_id,
                'rejected',
                [
                    'leave_plan_id' => $event->leavePlan->id,
                    'rejection_reason' => $event->rejectionReason,
                ],
                true,
                true
            );
        }
    }
}

