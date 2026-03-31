<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\SMSService;
use Illuminate\Console\Command;

class DeploymentReadinessCheck extends Command
{
    protected $signature = 'app:deployment-readiness';
    protected $description = 'Check deployment readiness for auth, mail, sms, and role setup';

    public function handle(): int
    {
        $this->info('Running deployment readiness checks...');
        $this->line(str_repeat('-', 60));

        $checksPassed = 0;
        $checksFailed = 0;

        $mailMailer = config('mail.default');
        if ($mailMailer === 'log') {
            $this->warn("MAIL: default mailer is '{$mailMailer}' (no real delivery).");
            $checksFailed++;
        } else {
            $this->info("MAIL: default mailer is '{$mailMailer}'");
            $checksPassed++;
        }

        $smsConfigured = app(SMSService::class)->isConfigured();
        if ($smsConfigured) {
            $this->info('SMS: provider settings are configured.');
            $checksPassed++;
        } else {
            $this->warn('SMS: provider settings are incomplete.');
            $checksFailed++;
        }

        $roles = ['super_admin', 'admin', 'hr', 'head_of_department', 'employee'];
        foreach ($roles as $role) {
            $exists = User::where('role', $role)->exists();
            if ($exists) {
                $this->info("ROLE: {$role} user exists.");
                $checksPassed++;
            } else {
                $this->warn("ROLE: {$role} user missing.");
                $checksFailed++;
            }
        }

        $this->line(str_repeat('-', 60));
        $this->info("Checks passed: {$checksPassed}");
        $this->info("Checks failed: {$checksFailed}");

        if ($checksFailed > 0) {
            $this->warn('System is close, but not fully deployment-ready. Resolve failed checks.');
            return self::FAILURE;
        }

        $this->info('System is deployment-ready based on configured checks.');
        return self::SUCCESS;
    }
}
