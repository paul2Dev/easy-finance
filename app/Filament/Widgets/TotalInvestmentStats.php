<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Investment;

class TotalInvestmentStats extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected function getStats(): array
    {
        // Calculate the total investment for ETFs and crypto
        $totalEtfInvestment = Investment::whereHas('instrument', function ($query) {
                $query->where('type', 'etf');
            })
            ->sum('total'); // Assuming 'total' is the field storing the money invested for each record

        $totalCryptoInvestment = Investment::whereHas('instrument', function ($query) {
                $query->where('type', 'crypto');
            })
            ->sum('total');

        $totalInvestment = $totalEtfInvestment + $totalCryptoInvestment;

        return [
            Stat::make('Total Investments', number_format($totalInvestment).' '. config('filament.investment_currency.code'))
                ->description('Total Investments')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Investments in ETFS', number_format($totalEtfInvestment).' '. config('filament.investment_currency.code'))
                ->description('Total ETFS Investments')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),
            Stat::make('Total Investments in Crypto', number_format($totalCryptoInvestment).' '. config('filament.investment_currency.code'))
                ->description('Total Crypto Investments')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success')
        ];
    }
}
