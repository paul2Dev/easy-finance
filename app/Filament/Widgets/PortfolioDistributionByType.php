<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Investment;
use Illuminate\Support\Facades\Auth;

class PortfolioDistributionByType extends ChartWidget
{
    protected static ?string $heading = 'Portfolio Distribution by Type';

    protected static bool $isDiscovered = false;

    protected function getData(): array
    {
        // Get total investment grouped by instrument type for the authenticated user
        $investments = Investment::with('instrument')
            ->get()
            ->groupBy('instrument.type');

        // Calculate the total investment for all types
        $totalInvestment = $investments->flatten()->sum('total');

        // Calculate percentages for each instrument type and format to 2 decimals
        $distributionByType = $investments->map(fn ($investments) =>
            number_format(($investments->sum('total') / $totalInvestment) * 100, 2)
        )->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Investment by Type (%)',
                    'data' => array_values($distributionByType),
                    'backgroundColor' => ['#3b82f6', '#f97316', '#22c55e'], // Customize colors
                ],
            ],
            'labels' => array_keys($distributionByType),
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'maintainAspectRatio' => false,
            'plugins' => [
                'legend' => [
                    'position' => 'right',
                ],
            ],
            'scales' => [
                'x' => [
                    'grid' => [
                        'display' => false, // Disable grid lines on x-axis
                    ],
                    'ticks' => [
                        'display' => false, // Disable ticks (numbers) on x-axis
                    ],
                ],
                'y' => [
                    'grid' => [
                        'display' => false, // Disable grid lines on y-axis
                    ],
                    'ticks' => [
                        'display' => false, // Disable ticks (numbers) on x-axis
                    ],
                ],
            ],
            'cutout' => 50,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
