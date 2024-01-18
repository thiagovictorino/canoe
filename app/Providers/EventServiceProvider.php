<?php

namespace App\Providers;

use App\Events\Fund\DuplicateFundWarningEvent;
use App\Events\Fund\FundCreatedEvent;
use App\Listeners\Fund\CheckFundDuplicity;
use App\Listeners\Fund\RegisterDuplicateFund;
use App\Models\Fund;
use App\Observers\FundObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        FundCreatedEvent::class => [
            CheckFundDuplicity::class,
        ],
        DuplicateFundWarningEvent::class => [
            RegisterDuplicateFund::class,
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        Fund::observe(FundObserver::class);
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
