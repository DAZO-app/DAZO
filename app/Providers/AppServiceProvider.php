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
        if (config('app.env') !== 'local') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }

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

        // Register community Socialite providers (OAuth / SSO)
        \Illuminate\Support\Facades\Event::listen(
            \SocialiteProviders\Manager\SocialiteWasCalled::class,
            \SocialiteProviders\Microsoft\MicrosoftExtendSocialite::class
        );
        \Illuminate\Support\Facades\Event::listen(
            \SocialiteProviders\Manager\SocialiteWasCalled::class,
            \SocialiteProviders\Apple\AppleExtendSocialite::class
        );
        \Illuminate\Support\Facades\Event::listen(
            \SocialiteProviders\Manager\SocialiteWasCalled::class,
            \SocialiteProviders\FranceConnect\FranceConnectExtendSocialite::class
        );
    }

}
