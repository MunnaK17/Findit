<?php

namespace App\Providers;

use App\Notifications\Channels\WhatsAppChannel;
use App\Services\WhatsAppService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Register WhatsApp channel via extend (Laravel 11 compatible)
        Notification::extend('whatsapp', function ($app) {
            return new WhatsAppChannel($app->make(WhatsAppService::class));
        });

        // Share unread count only; full notification lists are owned by their controllers/API.
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $user = Auth::user();
                $sharedUnreadCount = $user->unreadNotifications()->count();

                $view->with(compact('sharedUnreadCount'));
            }
        });
    }
}