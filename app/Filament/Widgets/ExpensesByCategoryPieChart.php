<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Expense;
use Carbon\Carbon;

class ExpensesByCategoryPieChart extends ChartWidget
{
    protected static ?string $heading = 'Current Month Expenses Distribution (%)';
    protected static ?int $sort = 3;

    protected function getData(): array
    {

        [$startOfMonth, $endOfMonth] = Carbon::now()->customMonthRange();

        // Query expenses grouped by category and sum their amounts
        $expensesByCategory = Expense::selectRaw('category_id, SUM(amount) as total')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->groupBy('category_id')
            ->with('category')
            ->get();

        // Calculate the total sum of expenses for the current month
        $totalExpenses = $expensesByCategory->sum('total');

        // Prepare data in percentage format
        $labels = $expensesByCategory->map(fn ($expense) => $expense->category->name)->toArray();
        $data = $expensesByCategory->map(fn ($expense) => round(($expense->total / $totalExpenses) * 100, 2))->toArray();

        return [
            'labels' => $labels, // Categories
            'datasets' => [
                [
                    'label' => 'Expense Distribution (%)',
                    'data' => $data, // Percentages
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
                        'display' => false, // Disable ticks (numbers) on y-axis
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
