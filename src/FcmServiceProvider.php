<?php

namespace DevKandil\NotiFire;

use DevKandil\NotiFire\Channels\FcmChannel;
use DevKandil\NotiFire\Contracts\FcmServiceInterface;
use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class FcmServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Configuration
        $this->publishes([
            __DIR__ . '/../config/fcm.php' => config_path('fcm.php'),
        ], 'fcm-config');

        // Migrations
        $this->publishes([
            __DIR__ . '/../database/migrations/' => database_path('migrations'),
        ], 'fcm-migrations');

        // Example Notification
        $this->publishes([
            __DIR__ . '/Notifications/ExampleNotification.php' => app_path('Notifications/ExampleNotification.php'),
        ], 'fcm-notifications');

        // Contracts
        $this->publishes([
            __DIR__ . '/Contracts' => app_path('Contracts'),
        ], 'fcm-contracts');

        // Enums
        $this->publishes([
            __DIR__ . '/Enums' => app_path('Enums'),
        ], 'fcm-enums');

        // Traits
        $this->publishes([
            __DIR__ . '/Traits' => app_path('Traits'),
        ], 'fcm-traits');

        $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
        
        // Register the FCM notification channel
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('fcm', function ($app) {
                return new FcmChannel($app->make(FcmService::class));
            });
        });
    }

    public function register(): void
    {

        $this->mergeConfigFrom(
            __DIR__ . '/../config/fcm.php',
            'fcm'
        );

        $this->app->singleton(FcmServiceInterface::class, function ($app) {
            return new FcmService();
        });

        $this->app->alias(FcmServiceInterface::class, FcmService::class);

        $this->app->alias(FcmService::class, 'fcm');
    }
}