<?php

namespace App\Listeners;

use App\Events\LeaveApplicationStatusChanged;
use App\Notifications\LeaveApplicationApproved;
use App\Notifications\LeaveApplicationRejected;
use App\Models\SystemNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleLeaveApplicationStatusNotifications implements ShouldQueue
{
    public function handle(LeaveApplicationStatusChanged $event): void
    {
        if ($event->status === 'approved') {
            $event->leaveApplication->user->notify(new LeaveApplicationApproved($event->leaveApplication));
            SystemNotification::sendLeaveApplicationNotification(
                $event->leaveApplication->user_id,
                'approved',
                [
                    'leave_application_id' => $event->leaveApplication->id,
                    'approved_by' => $event->actor->full_name,
                ],
                true,
                true
            );
            return;
        }

        if ($event->status === 'rejected') {
            $event->leaveApplication->user->notify(
                new LeaveApplicationRejected($event->leaveApplication, (string) $event->remarks)
            );
            SystemNotification::sendLeaveApplicationNotification(
                $event->leaveApplication->user_id,
                'rejected',
                [
                    'leave_application_id' => $event->leaveApplication->id,
                    'rejected_by' => $event->actor->full_name,
                    'rejection_reason' => $event->remarks,
                ],
                true,
                true
            );
        }
    }
}

