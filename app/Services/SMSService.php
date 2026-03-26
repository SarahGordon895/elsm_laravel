<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class SMSService
{
    protected $client;
    protected $fromNumber;

    public function __construct()
    {
        $this->fromNumber = config('services.twilio.from');
        
        $sid = config('services.twilio.sid');
        $token = config('services.twilio.token');
        
        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    /**
     * Send SMS notification
     */
    public function sendSMS($to, $message): bool
    {
        try {
            if (!$this->client) {
                Log::warning('Twilio client not configured. SMS not sent.');
                return false;
            }

            // Format phone number (remove any non-digit characters except +)
            $formattedPhone = $this->formatPhoneNumber($to);
            
            if (!$formattedPhone) {
                Log::warning("Invalid phone number format: {$to}");
                return false;
            }

            $message = $this->client->messages->create(
                $formattedPhone,
                [
                    'from' => $this->fromNumber,
                    'body' => $message
                ]
            );

            Log::info("SMS sent to {$formattedPhone}: {$message}");
            return true;

        } catch (\Exception $e) {
            Log::error("SMS sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send SMS to user
     */
    public function sendToUser(User $user, $message): bool
    {
        if (!$user->phone_number) {
            Log::warning("User {$user->email} has no phone number");
            return false;
        }

        return $this->sendSMS($user->phone_number, $message);
    }

    /**
     * Send leave application notification
     */
    public function sendLeaveApplicationNotification(User $user, string $leaveType, string $dates): bool
    {
        $message = "ELMS: {$user->full_name} has applied for {$leaveType} leave from {$dates}. Please review the application.";
        return $this->sendToUser($user, $message);
    }

    /**
     * Send leave approval notification
     */
    public function sendLeaveApprovalNotification(User $user, string $leaveType, string $dates): bool
    {
        $message = "ELMS: Your {$leaveType} leave from {$dates} has been approved. Enjoy your time off!";
        return $this->sendToUser($user, $message);
    }

    /**
     * Send leave rejection notification
     */
    public function sendLeaveRejectionNotification(User $user, string $leaveType, string $dates, string $reason = null): bool
    {
        $message = "ELMS: Your {$leaveType} leave from {$dates} has been rejected.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }
        return $this->sendToUser($user, $message);
    }

    /**
     * Send leave plan notification
     */
    public function sendLeavePlanNotification(User $user, string $action, string $leaveType = null): bool
    {
        $messages = [
            'created' => "ELMS: Your leave plan" . ($leaveType ? " for {$leaveType}" : "") . " has been created and is pending HR approval.",
            'approved' => "ELMS: Your leave plan" . ($leaveType ? " for {$leaveType}" : "") . " has been approved!",
            'rejected' => "ELMS: Your leave plan" . ($leaveType ? " for {$leaveType}" : "") . " has been rejected. Please contact HR for details."
        ];

        $message = $messages[$action] ?? $messages['created'];
        return $this->sendToUser($user, $message);
    }

    /**
     * Send welcome notification
     */
    public function sendWelcomeNotification(User $user): bool
    {
        $message = "ELMS: Welcome {$user->full_name}! Your account has been created. Login at " . config('app.url');
        return $this->sendToUser($user, $message);
    }

    /**
     * Format phone number for Twilio
     */
    protected function formatPhoneNumber($phone): ?string
    {
        // Remove all non-digit characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Ensure it starts with + for international format
        if (strlen($phone) === 10 && !str_starts_with($phone, '+')) {
            // Assume US number if 10 digits
            $phone = '+1' . $phone;
        } elseif (strlen($phone) === 11 && !str_starts_with($phone, '+')) {
            // Assume US number if 11 digits
            $phone = '+' . $phone;
        } elseif (!str_starts_with($phone, '+')) {
            // Add + if missing
            $phone = '+' . $phone;
        }
        
        // Validate phone number format (basic validation)
        if (!preg_match('/^\+[1-9]\d{1,14}$/', $phone)) {
            return null;
        }
        
        return $phone;
    }

    /**
     * Check if SMS service is configured
     */
    public function isConfigured(): bool
    {
        return $this->client !== null;
    }
}
