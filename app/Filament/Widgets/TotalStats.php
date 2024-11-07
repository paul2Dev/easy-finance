<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class TotalStats extends BaseWidget
{

    protected function getStats(): array
    {
        $userId = Auth::id();
        $startOfMonth = Carbon::now()->startOfMonth();

        // Find the last day in the current month with an Income for the user
        $lastIncomeDate = Income::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, Carbon::now()->endOfMonth()])
            ->latest('date')
            ->value('date');

        // If no Incomes are found, default to today
        $endDate = $lastIncomeDate ? Carbon::parse($lastIncomeDate) : Carbon::now();

        // Initialize an array to store daily Incomes up to the last Income date
        $dailyIncomes = [];

        // Loop through each day from the start of the month to the last Income date
        foreach ($startOfMonth->daysUntil($endDate->addDay()) as $day) {
            // Get total Incomes for the current day
            $dailyTotal = Income::where('user_id', $userId)
                ->whereDate('date', $day)
                ->sum('amount');

            // Add daily total to the array
            $dailyIncomes[] = $dailyTotal;
        }

        // Calculate the total Incomes for the current month up to the last Income date
        $totalMonthlyIncomes = number_format(array_sum($dailyIncomes)) .' '. config('filament.currency.code');

        // Find the last day in the current month with an expense for the user
        $lastExpenseDate = Expense::where('user_id', $userId)
            ->whereBetween('date', [$startOfMonth, Carbon::now()->endOfMonth()])
            ->latest('date')
            ->value('date');

        // If no expenses are found, default to today
        $endDate = $lastExpenseDate ? Carbon::parse($lastExpenseDate) : Carbon::now();

        // Initialize an array to store daily expenses up to the last expense date
        $dailyExpenses = [];

        // Loop through each day from the start of the month to the last expense date
        foreach ($startOfMonth->daysUntil($endDate->addDay()) as $day) {
            // Get total expenses for the current day
            $dailyTotal = Expense::where('user_id', $userId)
                ->whereDate('date', $day)
                ->sum('amount');

            // Add daily total to the array
            $dailyExpenses[] = $dailyTotal;
        }

        // Calculate the total expenses for the current month up to the last expense date
        $totalMonthlyExpenses = number_format(array_sum($dailyExpenses)) .' '. config('filament.currency.code');

        return [
            Stat::make('Incomes', $totalMonthlyIncomes)
                ->description('Total Incomes for this month')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success')
                ->chart($dailyIncomes), // Pass daily Incomes up to the last Income day to the chart
            Stat::make('Expenses', $totalMonthlyExpenses)
                ->description('Total Expenses for this month')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color('danger')
                ->chart($dailyExpenses), // Pass daily expenses up to the last expense day to the chart
            Stat::make('Cash Flow', number_format(array_sum($dailyIncomes) - array_sum($dailyExpenses)))
                ->description('Remaining cash flow for this month')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('warning')

        ];
    }
}
