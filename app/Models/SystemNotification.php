<?php

namespace App\Models;

use App\Services\SMSService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;

class SystemNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'channel',
        'is_read',
        'read_at',
        'data',
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
        'data' => 'array',
    ];

    /**
     * Get the user that owns the notification.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Mark notification as read.
     */
    public function markAsRead(): bool
    {
        return $this->update([
            'is_read' => true,
            'read_at' => now(),
        ]);
    }

    /**
     * Scope to get unread notifications.
     */
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    /**
     * Scope to get read notifications.
     */
    public function scopeRead($query)
    {
        return $query->where('is_read', true);
    }

    /**
     * Scope to get notifications by type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope to get notifications by channel.
     */
    public function scopeByChannel($query, string $channel)
    {
        return $query->where('channel', $channel);
    }

    /**
     * Create a new notification with multi-channel support.
     */
    public static function createNotification(
        int $userId,
        string $type,
        string $title,
        string $message,
        string $channel = 'system',
        array $data = null,
        bool $sendEmail = false,
        bool $sendSMS = false
    ): self {
        $user = User::find($userId);
        $resolvedChannels = self::resolveChannels($user, $type);
        $sendEmail = $sendEmail && $resolvedChannels['email'] && !empty($user?->email);
        $sendSMS = $sendSMS && $resolvedChannels['sms'] && !empty($user?->phone_number);
        $allowSystem = $resolvedChannels['system'];
        $forceSystem = (bool) data_get($data, 'force_system', false);

        $notification = self::create([
            'user_id' => $userId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'channel' => $channel,
            // Preserve records for audit even when system notifications are muted.
            'is_read' => !$allowSystem && !$forceSystem,
            'data' => $data,
        ]);

        if ($user) {
            // Send email notification
            if ($sendEmail) {
                try {
                    Mail::raw($message, function ($mail) use ($user, $title) {
                        $mail->to($user->email)
                            ->subject($title);
                    });
                } catch (\Exception $e) {
                    \Log::error("Email sending failed: " . $e->getMessage());
                }
            }

            // Send SMS notification
            if ($sendSMS) {
                $smsService = new SMSService();
                $smsService->sendToUser($user, $message);
            }
        }

        return $notification;
    }

    private static function resolveChannels(?User $user, string $type): array
    {
        if (!$user) {
            return ['system' => true, 'email' => true, 'sms' => true];
        }

        $global = [
            'system' => $user->allowsSystemNotifications(),
            'email' => $user->allowsEmailNotifications(),
            'sms' => $user->allowsSmsNotifications(),
        ];

        $preference = $user->notificationPreferences()->where('event_type', $type)->first();
        if (!$preference) {
            return $global;
        }

        return [
            'system' => $preference->notify_system ?? $global['system'],
            'email' => $preference->notify_email ?? $global['email'],
            'sms' => $preference->notify_sms ?? $global['sms'],
        ];
    }

    /**
     * Send leave plan notification with multi-channel support.
     */
    public static function sendLeavePlanNotification(
        int $userId,
        string $type, // created, approved, rejected
        array $data = [],
        bool $sendEmail = true,
        bool $sendSMS = true
    ): self {
        $messages = [
            'created' => [
                'title' => 'Leave Plan Created',
                'message' => 'Your leave plan has been created and is pending HR approval.',
            ],
            'approved' => [
                'title' => 'Leave Plan Approved',
                'message' => 'Your leave plan has been approved by HR.',
            ],
            'rejected' => [
                'title' => 'Leave Plan Rejected',
                'message' => 'Your leave plan has been rejected by HR. Please check the rejection reason.',
            ],
        ];

        $notificationData = $messages[$type] ?? $messages['created'];
        
        return self::createNotification(
            $userId,
            "leave_plan_{$type}",
            $notificationData['title'],
            $notificationData['message'],
            'system',
            array_merge($data, ['leave_plan_type' => $type]),
            $sendEmail,
            $sendSMS
        );
    }

    /**
     * Send leave application notification with multi-channel support.
     */
    public static function sendLeaveApplicationNotification(
        int $userId,
        string $type, // applied, approved, rejected
        array $data = [],
        bool $sendEmail = true,
        bool $sendSMS = true
    ): self {
        $messages = [
            'applied' => [
                'title' => 'Leave Application Submitted',
                'message' => 'Your leave application has been submitted and is pending approval.',
            ],
            'approved' => [
                'title' => 'Leave Application Approved',
                'message' => 'Your leave application has been approved.',
            ],
            'rejected' => [
                'title' => 'Leave Application Rejected',
                'message' => 'Your leave application has been rejected. Please check the rejection reason.',
            ],
        ];

        $notificationData = $messages[$type] ?? $messages['applied'];
        
        return self::createNotification(
            $userId,
            "leave_application_{$type}",
            $notificationData['title'],
            $notificationData['message'],
            'system',
            array_merge($data, ['application_type' => $type]),
            $sendEmail,
            $sendSMS
        );
    }

    /**
     * Send HR notification for leave application with multi-channel support.
     */
    public static function sendHRLeaveNotification(
        int $hrUserId,
        string $employeeName,
        string $leaveType,
        string $dates,
        bool $sendEmail = true,
        bool $sendSMS = true
    ): self {
        $message = "{$employeeName} has applied for {$leaveType} leave from {$dates}.";
        
        return self::createNotification(
            $hrUserId,
            'hr_leave_application',
            'New Leave Application',
            $message,
            'system',
            [
                'employee_name' => $employeeName,
                'leave_type' => $leaveType,
                'dates' => $dates,
            ],
            $sendEmail,
            $sendSMS
        );
    }

    /**
     * Send welcome notification with multi-channel support.
     */
    public static function sendWelcomeNotification(
        int $userId,
        bool $sendEmail = true,
        bool $sendSMS = true
    ): ?self {
        $user = User::find($userId);
        if (!$user) {
            return null;
        }

        $message = "Welcome {$user->full_name}! Your account has been created. Login at " . config('app.url');
        
        return self::createNotification(
            $userId,
            'welcome',
            'Welcome to ELMS',
            $message,
            'system',
            ['user_name' => $user->full_name],
            $sendEmail,
            $sendSMS
        );
    }
}
