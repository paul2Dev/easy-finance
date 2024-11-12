<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Investment;
use Illuminate\Support\Facades\Auth;

class PortfolioDistributionByName extends ChartWidget
{
    protected static ?string $heading = 'Portfolio Distribution by Instrument Name';

    protected static bool $isDiscovered = false;

    protected function getData(): array
    {
        // Get total investment grouped by instrument name for the authenticated user
        $investments = Investment::with('instrument')
            ->where('user_id', Auth::id())
            ->get()
            ->groupBy('instrument.name');

        // Calculate the total investment for all names
        $totalInvestment = $investments->flatten()->sum('total');

        // Calculate percentages for each instrument name and format to 2 decimals
        $distributionByName = $investments->map(fn ($investments) =>
            number_format(($investments->sum('total') / $totalInvestment) * 100, 2)
        )->toArray();

        return [
            'datasets' => [
                [
                    'label' => 'Investment by Name (%)',
                    'data' => array_values($distributionByName),
                    'backgroundColor' => ['#3b82f6', '#f97316', '#22c55e', '#eab308'], // Customize colors
                ],
            ],
            'labels' => array_keys($distributionByName),
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
