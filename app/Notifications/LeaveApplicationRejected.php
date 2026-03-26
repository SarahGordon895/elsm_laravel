<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class LeaveApplicationRejected extends Notification implements ShouldQueue
{
    use Queueable;

    protected $leaveApplication;
    protected $rejectionReason;

    public function __construct($leaveApplication, $rejectionReason = null)
    {
        $this->leaveApplication = $leaveApplication;
        $this->rejectionReason = $rejectionReason;
    }

    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        $mail = (new MailMessage)
            ->subject('Your Leave Application Has Been Rejected')
            ->greeting('Dear ' . $notifiable->first_name)
            ->line('Your leave application has been reviewed and rejected by ' . $this->leaveApplication->approver->full_name . '.')
            ->line('Leave Type: ' . $this->leaveApplication->leaveType->name)
            ->line('Duration: ' . $this->leaveApplication->start_date->format('M d, Y') . ' to ' . $this->leaveApplication->end_date->format('M d, Y'));

        if ($this->rejectionReason) {
            $mail->line('Reason: ' . $this->rejectionReason);
        }

        return $mail
            ->line('If you have any questions or need clarification, please contact your manager or HR.')
            ->action('View Application', route('leave-applications.show', $this->leaveApplication))
            ->line('You may submit a new leave application if needed.');
    }

    public function toDatabase($notifiable)
    {
        return [
            'leave_application_id' => $this->leaveApplication->id,
            'type' => 'leave_application_rejected',
            'title' => 'Leave Application Rejected',
            'message' => 'Your ' . $this->leaveApplication->leaveType->name . ' application has been rejected',
            'icon' => 'times-circle',
            'color' => 'red',
        ];
    }
}
