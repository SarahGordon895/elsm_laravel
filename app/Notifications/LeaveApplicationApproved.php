<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class LeaveApplicationApproved extends Notification implements ShouldQueue
{
    use Queueable;

    protected $leaveApplication;

    public function __construct($leaveApplication)
    {
        $this->leaveApplication = $leaveApplication;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Your Leave Application Has Been Approved')
            ->greeting('Congratulations ' . $notifiable->first_name)
            ->line('Your leave application has been approved by ' . $this->leaveApplication->approver->full_name . '.')
            ->line('Leave Type: ' . $this->leaveApplication->leaveType->name)
            ->line('Duration: ' . $this->leaveApplication->start_date->format('M d, Y') . ' to ' . $this->leaveApplication->end_date->format('M d, Y'))
            ->line('Total Days: ' . $this->leaveApplication->start_date->diffInDays($this->leaveApplication->end_date) + 1)
            ->line('Please ensure all your work responsibilities are properly handed over before your leave.')
            ->action('View Application', route('leave-applications.show', $this->leaveApplication))
            ->line('Enjoy your leave!');
    }

    public function toDatabase($notifiable)
    {
        return [
            'leave_application_id' => $this->leaveApplication->id,
            'type' => 'leave_application_approved',
            'title' => 'Leave Application Approved',
            'message' => 'Your ' . $this->leaveApplication->leaveType->name . ' application has been approved',
            'icon' => 'check-circle',
            'color' => 'green',
        ];
    }
}
