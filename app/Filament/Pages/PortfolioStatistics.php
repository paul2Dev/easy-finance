<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\InvestmentInstrumentsTable;
use Filament\Pages\Page;
use App\Filament\Widgets\PortfolioDistributionByType;
use App\Filament\Widgets\PortfolioDistributionByName;
use App\Filament\Widgets\TotalInvestmentStats;
use App\Jobs\UpdateInvestmentInstrumentPrices;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;
use Filament\Pages\Actions\Action;



class PortfolioStatistics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-pie';
    protected static ?string $navigationLabel = 'Portfolio Statistics';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.pages.portfolio-statistics';

    protected function getHeaderWidgets(): array
    {
        return [
            TotalInvestmentStats::class,
            PortfolioDistributionByType::class,
            PortfolioDistributionByName::class,
            InvestmentInstrumentsTable::class,
        ];
    }

    public function getActions(): array
    {
        return [
            Action::make('update_prices')
                ->label('Update Prices')
                ->icon('heroicon-o-chart-pie')
                ->color('success')
                ->action(function () {
                    // Dispatch the job to update prices for all the user's investment instruments
                    UpdateInvestmentInstrumentPrices::dispatch(Auth::user());

                    // Notify the user that the prices are being updated
                    Notification::make()
                        ->title('Prices Update Started')
                        ->success()
                        ->send();
                })
                ->button()
                ->requiresConfirmation() // Enables confirmation modal
                ->modalHeading('Confirm Price Update') // Title of the modal
                ->modalSubheading('Are you sure you want to update the prices for your investment instruments?') // Description in the modal
                ->modalButton('Yes, Update Prices') // Text on the confirm button
        ];
    }
}
