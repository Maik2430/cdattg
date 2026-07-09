<?php

namespace App\Providers;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Notifications\Channels\DatabaseChannel;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class NotificationServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Sobrescribir el canal de base de datos para usar nombres en español
        Notification::resolved(function (ChannelManager $service) {
            $service->extend('database', function ($app) {
                return new class($app->make('db')) extends DatabaseChannel
                {
                    protected function buildPayload($notifiable, \Illuminate\Notifications\Notification $notification)
                    {
                        return [
                            'id' => $notification->id,
                            'tipo' => get_class($notification),
                            'notificable_type' => get_class($notifiable),
                            'notificable_id' => $notifiable->getKey(),
                            'datos' => $this->getData($notifiable, $notification),
                            'leida_en' => null,
                        ];
                    }
                };
            });
        });
    }
}
