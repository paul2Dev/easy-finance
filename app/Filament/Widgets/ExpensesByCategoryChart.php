<?php

namespace App\Filament\Widgets;

use App\Models\Expense;
use App\Models\Budget;
use App\Models\Category;
use Filament\Widgets\ChartWidget;
use Carbon\Carbon;

class ExpensesByCategoryChart extends ChartWidget
{
    protected static ?string $heading = 'Total Expenses vs Budget by Category (Current Month)';

    protected static ?int $sort = 4;

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getData(): array
    {
        [$startOfMonth, $endOfMonth] = Carbon::now()->customMonthRange();

        // Eager load category relationships for expenses and budgets
        $expenses = Expense::with('category')
            ->whereBetween('date', [$startOfMonth, $endOfMonth])
            ->get();

        $budgets = Budget::with('category')
            ->whereIn('category_id', $expenses->pluck('category_id'))
            ->get();

        // Use collections to group and sum data
        $expenseData = $expenses->groupBy('category_id')->map(fn($expenses) => $expenses->sum('amount'));
        $budgetData = $budgets->groupBy('category_id')->map(fn($budgets) => $budgets->sum('amount'));

        // Fetch all necessary categories in a single query
        $categories = Category::whereIn('id', $expenseData->keys()->merge($budgetData->keys()))->get();

        // Map data for the chart
        $categoryLabels = [];
        $expenseValues = [];
        $budgetValues = [];

        foreach ($categories as $category) {
            $categoryLabels[] = $category->name;
            $expenseValues[] = $expenseData->get($category->id, 0);
            $budgetValues[] = $budgetData->get($category->id, 0);
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
