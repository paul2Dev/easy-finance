<?php

namespace App\Providers;

use Filament\Actions\CreateAction;
use Illuminate\Support\ServiceProvider;
use Filament\Tables\Actions\EditAction;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;


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

        EditAction::configureUsing(function (EditAction $action) {
            Log::info('EditAction global configuration applied.');
            $action->slideOver();
        });

        //i want the same thing for the CreateAction with the corrent CreateAction namespace

        CreateAction::configureUsing(function (CreateAction $action) {
            Log::info('CreateAction global configuration applied.');
            $action->slideOver();
        });

        // Add a Carbon macro for the custom month range
        Carbon::macro('customMonthRange', function (): array {

            $now = $this;

            if ($now->day >= 10) {
                $startOfPeriod = $now->copy()->startOfMonth()->addDays(9); // 10th of this month
                $endOfPeriod = $now->copy()->startOfMonth()->addMonth()->addDays(9); // 9th of next month
            } else {
                $startOfPeriod = $now->copy()->subMonth()->startOfMonth()->addDays(9); // 10th of last month
                $endOfPeriod = $now->copy()->startOfMonth()->addDays(9); // 9th of this month
            }

            return [$startOfPeriod, $endOfPeriod];
        });
    }
}
