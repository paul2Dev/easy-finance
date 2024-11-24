<?php

namespace App\Providers;

use Filament\Actions\CreateAction;
use Illuminate\Support\ServiceProvider;
use Filament\Tables\Actions\EditAction;
use Illuminate\Support\Facades\Log;

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

    }
}
