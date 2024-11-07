<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Category;
use App\Models\Expense;

class ExpensesByCategoryPieChart extends ChartWidget
{
    protected static ?string $heading = 'Expenses by Category';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        // Query expenses grouped by category and sum their amounts
        $expensesByCategory = Expense::selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->with('category') // Make sure category relationship is loaded
            ->get();

        // Prepare data for chart
        $labels = $expensesByCategory->map(fn ($expense) => $expense->category->name)->toArray();
        $data = $expensesByCategory->map(fn ($expense) => $expense->total)->toArray();

        return [
            'labels' => $labels, // Categories
            'datasets' => [
                [
                    'label' => 'Total Expenses',
                    'data' => $data, // Amounts
                    'backgroundColor' => [
                        '#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40'
                    ], // Customize colors
                ],
            ],
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
