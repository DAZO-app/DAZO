<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\Event::listen(
            \App\Events\DecisionCreated::class,
            \App\Listeners\SendDecisionCreatedNotification::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\DecisionTransitioned::class,
            \App\Listeners\SendDecisionTransitionedNotification::class
        );

        \Illuminate\Support\Facades\Event::listen(
            \App\Events\FeedbackSubmitted::class,
            \App\Listeners\SendFeedbackSubmittedNotification::class
        );
    }

}
