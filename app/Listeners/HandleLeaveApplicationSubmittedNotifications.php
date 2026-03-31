<?php

namespace App\Listeners;

use App\Events\LeaveApplicationSubmitted;
use App\Models\SystemNotification;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;

class HandleLeaveApplicationSubmittedNotifications implements ShouldQueue
{
    public function handle(LeaveApplicationSubmitted $event): void
    {
        $hrUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'hr');
        })->get();

        foreach ($hrUsers as $hr) {
            SystemNotification::sendHRLeaveNotification(
                $hr->id,
                $event->user->full_name,
                $event->leaveType->name,
                $event->leaveApplication->start_date . ' to ' . $event->leaveApplication->end_date,
                true,
                true
            );
        }

        SystemNotification::sendLeaveApplicationNotification(
            $event->user->id,
            'applied',
            [
                'leave_application_id' => $event->leaveApplication->id,
                'leave_type' => $event->leaveType->name,
                'dates' => $event->leaveApplication->start_date . ' to ' . $event->leaveApplication->end_date,
            ],
            true,
            true
        );

        if ($event->sickExtraAnnualDeduction > 0) {
            foreach ($hrUsers as $hr) {
                SystemNotification::createNotification(
                    $hr->id,
                    'hr_sick_leave_proof_missing',
                    'Sick Leave Proof Missing - Annual Deduction Applied',
                    "{$event->user->full_name} applied {$event->requestedDays} day(s) sick leave without doctor proof. {$event->sickExtraAnnualDeduction} day(s) deducted from Annual Leave.",
                    'system',
                    [
                        'employee_id' => $event->user->id,
                        'leave_application_id' => $event->leaveApplication->id,
                        'sick_days' => $event->requestedDays,
                        'deducted_annual_days' => $event->sickExtraAnnualDeduction,
                    ],
                    true,
                    false
                );
            }
        }
    }
}

