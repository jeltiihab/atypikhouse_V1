<?php

namespace App\Providers;

use App\Models\Attribute;
use App\Models\Category;
use App\Models\Reservation;
use App\Observers\AttributeObserver;
use App\Observers\CategoryObserver;
use App\Observers\ReservationObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        Attribute::observe(AttributeObserver::class);
        Reservation::observe(ReservationObserver::class);
    }
}
