<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Services\ContractService;
use App\Services\RentScheduleService;
use App\Services\LatePenaltyService;
use App\Services\PaymentService;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Pagination\Paginator;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind services as singletons
        $this->app->singleton(RentScheduleService::class);
        $this->app->singleton(LatePenaltyService::class);
        $this->app->singleton(NotificationService::class);
        $this->app->singleton(ContractService::class);
        $this->app->singleton(PaymentService::class);
    }

    public function boot(): void
    {
        // Use Bootstrap 5 for pagination
        Paginator::useBootstrapFive();

        // Set Carbon locale for Arabic dates
        Carbon::setLocale('ar');

        // Share unread notifications count with all views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $unread = Auth::user()->unreadNotificationsCount();
                $view->with('unreadNotificationsCount', $unread);
            }
        });
    }
}
