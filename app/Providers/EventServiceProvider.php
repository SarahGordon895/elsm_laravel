<?php

namespace App\Providers;

use App\Events\LeaveApplicationStatusChanged;
use App\Events\LeaveApplicationSubmitted;
use App\Events\LeavePlanCreated;
use App\Events\LeavePlanStatusChanged;
use App\Listeners\HandleLeaveApplicationStatusNotifications;
use App\Listeners\HandleLeaveApplicationSubmittedNotifications;
use App\Listeners\HandleLeavePlanCreatedNotifications;
use App\Listeners\HandleLeavePlanStatusNotifications;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        LeaveApplicationSubmitted::class => [
            HandleLeaveApplicationSubmittedNotifications::class,
        ],
        LeaveApplicationStatusChanged::class => [
            HandleLeaveApplicationStatusNotifications::class,
        ],
        LeavePlanCreated::class => [
            HandleLeavePlanCreatedNotifications::class,
        ],
        LeavePlanStatusChanged::class => [
            HandleLeavePlanStatusNotifications::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
