<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Budget;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class ExpensesByCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Total Expenses vs Budget by Category (Current Month)';

    protected static ?int $sort = 2;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        // Fetch expense data grouped by category ID for the current month
        $expenseData = Expense::with('category')
            ->whereMonth('date', $currentMonth)
            ->whereYear('date', $currentYear)
            ->get()
            ->groupBy('category_id')
            ->map(function ($expenses) {
                return $expenses->sum('amount');
            });

        // Fetch budget data grouped by category ID
        $budgetData = Budget::with('category')
            ->whereIn('category_id', $expenseData->keys())
            ->get()
            ->groupBy('category_id')
            ->map(function ($budgets) {
                return $budgets->sum('amount');
            });

        // Prepare data for chart labels and datasets
        $categories = $expenseData->keys()->merge($budgetData->keys())->unique();
        $expenseValues = [];
        $budgetValues = [];
        $categoryLabels = [];

        foreach ($categories as $categoryId) {
            $category = \App\Models\Category::find($categoryId);
            $categoryLabels[] = $category ? $category->name : 'Unknown';
            $expenseValues[] = $expenseData->get($categoryId, 0);
            $budgetValues[] = $budgetData->get($categoryId, 0);
        }

        return [
            'labels' => $categoryLabels,
            'datasets' => [
                [
                    'label' => 'Expenses',
                    'data' => $expenseValues,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Budget',
                    'data' => $budgetValues,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }
}
