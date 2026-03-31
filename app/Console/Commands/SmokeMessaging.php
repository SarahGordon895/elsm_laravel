<?php

namespace App\Console\Commands;

use App\Models\SystemNotification;
use App\Models\User;
use App\Services\SMSService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class SmokeMessaging extends Command
{
    protected $signature = 'app:smoke-messaging {--email=} {--phone=}';
    protected $description = 'Run one email and one SMS live smoke test';

    public function handle(): int
    {
        $email = $this->option('email');
        $phone = $this->option('phone');

        $emailUser = $email
            ? User::where('email', $email)->first()
            : User::whereNotNull('email')->first();

        if (!$emailUser) {
            $this->error('No user available for email smoke test.');
            return self::FAILURE;
        }

        // Force SMTP for a true delivery attempt instead of log mailer.
        Config::set('mail.default', 'smtp');

        $this->info("Email target: {$emailUser->email}");
        try {
            $message = 'This is a live SMTP smoke test from ELMS.';
            Mail::raw($message, function ($mail) use ($emailUser) {
                $mail->to($emailUser->email)->subject('ELMS Email Smoke Test');
            });
            SystemNotification::createNotification(
                $emailUser->id,
                'ops_email_smoke',
                'ELMS Email Smoke Test',
                $message,
                'system',
                ['source' => 'app:smoke-messaging'],
                false,
                false
            );
            $this->info('Email smoke result: SENT_SUCCESS');
        } catch (\Throwable $e) {
            $this->error('Email smoke result: SEND_FAILED');
            $this->line($e->getMessage());
        }

        $smsUser = null;
        if ($phone) {
            $smsUser = User::where('phone_number', $phone)->first();
        }
        if (!$smsUser) {
            $smsUser = User::whereNotNull('phone_number')->where('phone_number', '!=', '')->first();
        }

        if (!$smsUser) {
            $this->warn('No user with phone_number found for SMS smoke test.');
            return self::SUCCESS;
        }

        $this->info("SMS target user: {$smsUser->email} ({$smsUser->phone_number})");
        $smsOk = app(SMSService::class)->sendToUser($smsUser, 'ELMS live SMS smoke test.');
        $this->info('SMS smoke result: ' . ($smsOk ? 'SENT_SUCCESS' : 'SEND_FAILED'));

        return self::SUCCESS;
    }
}
