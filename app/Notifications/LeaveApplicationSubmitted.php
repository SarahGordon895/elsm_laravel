<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class LeaveApplicationSubmitted extends Notification implements ShouldQueue
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
            ->subject('New Leave Application Submitted')
            ->greeting('Hello ' . $notifiable->first_name)
            ->line($this->leaveApplication->user->full_name . ' has submitted a new leave application.')
            ->line('Leave Type: ' . $this->leaveApplication->leaveType->name)
            ->line('Duration: ' . $this->leaveApplication->start_date->format('M d, Y') . ' to ' . $this->leaveApplication->end_date->format('M d, Y'))
            ->line('Reason: ' . $this->leaveApplication->reason)
            ->action('Review Application', route('leave-applications.show', $this->leaveApplication))
            ->line('Please review and take appropriate action.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'leave_application_id' => $this->leaveApplication->id,
            'type' => 'leave_application_submitted',
            'title' => 'New Leave Application',
            'message' => $this->leaveApplication->user->full_name . ' submitted a ' . $this->leaveApplication->leaveType->name . ' application',
            'icon' => 'calendar-plus',
            'color' => 'blue',
        ];
    }
}
