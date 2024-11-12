<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Filament\Widgets\PortfolioDistributionByType;
use App\Filament\Widgets\PortfolioDistributionByName;

class PortfolioStatistics extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?string $navigationLabel = 'Portfolio Statistics';

    protected static ?int $navigationSort = 6;

    protected static string $view = 'filament.pages.portfolio-statistics';

    protected function getHeaderWidgets(): array
    {
        return [
            PortfolioDistributionByType::class,
            PortfolioDistributionByName::class,
        ];
    }
}
