<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\InvestmentInstrumentsTable;
use Filament\Pages\Page;
use App\Filament\Widgets\PortfolioDistributionByType;
use App\Filament\Widgets\PortfolioDistributionByName;
use App\Filament\Widgets\TotalInvestmentStats;


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
}
