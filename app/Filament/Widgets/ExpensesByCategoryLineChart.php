<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Expense;
use Carbon\Carbon;

class ExpensesByCategoryLineChart extends ChartWidget
{

    protected static ?string $heading = 'Current Month Expenses';

    protected static ?int $sort = 2;

    // Use 'line' for line chart
    protected function getType(): string
    {
        return 'line';
    }

    // Fetch the data and return it in the correct format for the chart
    protected function getData(): array
    {

        $startOfMonth = Carbon::now()->startOfMonth()->addDays(9);
        $endOfMonth = Carbon::now()->endOfMonth()->addDays(10);

        // Query expenses for the current month grouped by category
        $expensesByCategory = Expense::selectRaw('category_id, amount, date')
            ->whereBetween('date', [$startOfMonth, $endOfMonth]) // Filter by current month
            ->with('category') // Make sure category relationship is loaded
            ->orderBy('date', 'asc')
            ->get();

        // Prepare data for chart
        $labels = $expensesByCategory->map(fn ($expense) => Carbon::parse($expense->date)->format('M-d') . ' '.$expense->category->name)->toArray();
        $data = $expensesByCategory->map(fn ($expense) => $expense->amount)->toArray();


        return [
            'datasets' => [
                [
                    'label' => 'Expenses',
                    'data' => $data,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.2)',
                    'tension' => 0.4,
                ],
            ],
            'labels' => $labels,
        ];
    }

}

