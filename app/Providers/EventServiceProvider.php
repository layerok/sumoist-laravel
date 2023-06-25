<?php

namespace App\Providers;

use App\Poster\Events\PosterWebhookReceived;
use App\Salesbox\Events\SalesboxWebhookReceived;
use App\SalesboxIntegration\Listeners\HandlePosterWebhook;
use App\SalesboxIntegration\Listeners\HandleSalesboxWebhook;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        PosterWebhookReceived::class => [
            HandlePosterWebhook::class
        ],
        SalesboxWebhookReceived::class => [
            HandleSalesboxWebhook::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
