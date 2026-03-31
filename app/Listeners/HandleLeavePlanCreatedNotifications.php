<?php

namespace App\Listeners;

use App\Events\LeavePlanCreated;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleLeavePlanCreatedNotifications implements ShouldQueue
{
    public function handle(LeavePlanCreated $event): void
    {
        $hrUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'hr');
        })->get();

        foreach ($hrUsers as $hr) {
            SystemNotification::createNotification(
                $hr->id,
                'leave_plan_created',
                'New Leave Plan Created',
                $event->actor->full_name . ' has created a leave plan for ' . $event->leaveType->name . ' and is awaiting your approval.',
                'system',
                [
                    'leave_plan_id' => $event->leavePlan->id,
                    'employee_name' => $event->actor->full_name,
                    'leave_type' => $event->leaveType->name,
                ],
                true,
                true
            );
        }

        SystemNotification::sendLeavePlanNotification(
            $event->actor->id,
            'created',
            ['leave_plan_id' => $event->leavePlan->id],
            true,
            true
        );
    }
}

