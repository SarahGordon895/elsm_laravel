<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class SMSService
{
    protected $apiUrl;
    protected $senderId;
    protected $apiToken;

    public function __construct()
    {
        $this->apiUrl = config('services.imartgroup.api_url', env('SMS_API_URL', 'https://smsservice.imartgroup.co.tz/api/v3/sms/send'));
        $this->senderId = config('services.imartgroup.sender_id', env('SMS_SENDER_ID', 'iMartGroup'));
        $this->apiToken = config('services.imartgroup.api_token', env('SMS_API_TOKEN'));
    }

    /**
     * Send SMS notification
     */
    public function sendSMS($to, $message): bool
    {
        try {
            if (!$this->isConfigured()) {
                Log::error('SMS service is not configured. Missing SMS_API_TOKEN or SMS provider settings.');
                return false;
            }

            // Format phone number
            $formattedPhone = $this->formatPhoneNumber($to);
            
            if (!$formattedPhone) {
                Log::warning("Invalid phone number format: {$to}");
                return false;
            }

            // Prepare the request data
            $data = [
                'sender_id' => $this->senderId,
                'recipient' => $formattedPhone,
                'message' => $message,
                'token' => $this->apiToken,
            ];

            // Send SMS via iMartGroup API
            $response = Http::timeout(20)->retry(2, 500)->post($this->apiUrl, $data);
            
            if ($response->successful()) {
                Log::info("SMS sent to {$formattedPhone}: {$message}");
                Log::info("SMS API Response: " . $response->body());
                return true;
            } else {
                Log::error("SMS API Error [{$response->status()}]: " . $response->body());
                return false;
            }

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
     * Send leave application notification to HR
     */
    public function sendLeaveApplicationToHR(User $employee, string $leaveType, string $dates): bool
    {
        $hrUsers = User::where('role', 'hr')->where('status', 'active')->get();
        $success = true;
        
        foreach ($hrUsers as $hr) {
            $message = "ELMS: {$employee->full_name} has applied for {$leaveType} leave from {$dates}. Please review the application.";
            if (!$this->sendToUser($hr, $message)) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Send leave approval notification to employee
     */
    public function sendLeaveApprovalNotification(User $user, string $leaveType, string $dates): bool
    {
        $message = "ELMS: Your {$leaveType} leave from {$dates} has been APPROVED. Enjoy your time off!";
        return $this->sendToUser($user, $message);
    }

    /**
     * Send leave rejection notification to employee
     */
    public function sendLeaveRejectionNotification(User $user, string $leaveType, string $dates, string $reason = null): bool
    {
        $message = "ELMS: Your {$leaveType} leave from {$dates} has been REJECTED.";
        if ($reason) {
            $message .= " Reason: {$reason}";
        }
        return $this->sendToUser($user, $message);
    }

    /**
     * Send leave plan notification to HR
     */
    public function sendLeavePlanToHR(User $employee, string $action, string $leaveType = null): bool
    {
        $hrUsers = User::where('role', 'hr')->where('status', 'active')->get();
        $success = true;
        
        foreach ($hrUsers as $hr) {
            $message = "ELMS: {$employee->full_name} has {$action} a leave plan" . ($leaveType ? " for {$leaveType}" : "") . ". Please review.";
            if (!$this->sendToUser($hr, $message)) {
                $success = false;
            }
        }
        
        return $success;
    }

    /**
     * Send leave plan notification to employee
     */
    public function sendLeavePlanToEmployee(User $user, string $action, string $leaveType = null): bool
    {
        $messages = [
            'created' => "ELMS: Your leave plan" . ($leaveType ? " for {$leaveType}" : "") . " has been submitted and is pending HR approval.",
            'approved' => "ELMS: Your leave plan" . ($leaveType ? " for {$leaveType}" : "") . " has been APPROVED!",
            'rejected' => "ELMS: Your leave plan" . ($leaveType ? " for {$leaveType}" : "") . " has been REJECTED. Please contact HR for details."
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
     * Format phone number for SMS
     */
    protected function formatPhoneNumber($phone): ?string
    {
        // Remove all non-digit characters except +
        $phone = preg_replace('/[^0-9+]/', '', $phone);
        
        // Handle Tanzanian phone numbers (start with 255, 07, or 06)
        if (strlen($phone) === 10 && str_starts_with($phone, '0')) {
            // Convert 07XXXXX to +2557XXXXX
            $phone = '+255' . substr($phone, 1);
        } elseif (strlen($phone) === 9 && str_starts_with($phone, '6')) {
            // Convert 6XXXXX to +2556XXXXX
            $phone = '+255' . $phone;
        } elseif (strlen($phone) === 12 && str_starts_with($phone, '255')) {
            // Already in international format without +
            $phone = '+' . $phone;
        } elseif (strlen($phone) === 13 && str_starts_with($phone, '+255')) {
            // Already properly formatted
            // Keep as is
        } elseif (!str_starts_with($phone, '+')) {
            // Add + if missing and assume international
            $phone = '+' . $phone;
        }
        
        // Validate phone number format (basic validation)
        if (!preg_match('/^\+[1-9]\d{8,14}$/', $phone)) {
            return null;
        }
        
        return $phone;
    }

    /**
     * Check if SMS service is configured
     */
    public function isConfigured(): bool
    {
        return !empty($this->apiUrl) && !empty($this->senderId) && !empty($this->apiToken);
    }
}
