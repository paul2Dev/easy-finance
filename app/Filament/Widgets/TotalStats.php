<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Expense;
use App\Models\Income;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use App\Models\Bill;

class TotalStats extends BaseWidget
{

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        [$startOfMonth, $endOfMonth] = Carbon::now()->customMonthRange();

        $totalIncomes = Income::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalMonthlyIncomes = number_format($totalIncomes) .' '. config('filament.currency.code');

        $totalExpenses = Expense::whereBetween('date', [$startOfMonth, $endOfMonth])
            ->sum('amount');

        $totalMonthlyExpenses = number_format($totalExpenses) .' '. config('filament.currency.code');


        $cashFlow = number_format($totalIncomes - $totalExpenses) .' '. config('filament.currency.code');

        // Count the number of pending bills for the current month
        $remainingBillsToPay = Bill::where('status', 'pending')->count();

        return [
            Stat::make('Incomes', $totalMonthlyIncomes)
                ->description('Total Incomes for this month')
                ->descriptionIcon('heroicon-o-arrow-trending-up')
                ->color('success'),
            Stat::make('Expenses', $totalMonthlyExpenses)
                ->description('Total Expenses for this month')
                ->descriptionIcon('heroicon-o-arrow-trending-down')
                ->color('danger'),
            Stat::make('Cash Flow', $cashFlow)
                ->description('Remaining cash flow for this month')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('warning'),
            Stat::make('Remaining Bills', $remainingBillsToPay)
                ->description('Number of bills remaining to be paid this month')
                ->descriptionIcon('heroicon-o-document-text') // Icon of your choice
                ->color('warning') // You can change the color based on your preference
        ];
    }
}
