<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Htmlable;

class ExpensesLineChart extends ChartWidget
{

    protected static ?string $heading = 'Total Expenses';

    protected static ?int $sort = 2;

    public ?string $filter = 'month';

    protected function getFilters(): ?array
    {
        return [
            'week' => 'Last Week',
            'month' => 'Last Month',
            '3months' => 'Last 3 Months',

        ];
    }

    // Use 'line' for line chart
    protected function getType(): string
    {
        return 'line';
    }

    // Fetch the data and return it in the correct format for the chart
    protected function getData(): array
    {
        $filter = $this->filter;

        //match filter using Flowframe\Trend\Trend

        [$starOfPeriod, $endOfPeriod] = Carbon::now()->customMonthRange();

        match ($filter) {
            'week' => [
                $startPeriod = Carbon::now()->startOfWeek(),
                $endPeriod = Carbon::now()->endOfWeek()
            ],
            'month' => [
                $startPeriod = $starOfPeriod,
                $endPeriod = $endOfPeriod
            ],
            '3months' => [
                $startPeriod = Carbon::now()->subMonths(3),
                $endPeriod = Carbon::now()->endOfMonth()
            ],
        };

        // Query expenses for the current month grouped by category
        $expenses = Expense::whereBetween('date', [$startPeriod, $endPeriod]) // Filter by current month
            ->with('category') // Make sure category relationship is loaded
            ->orderBy('date', 'asc')
            ->get();

        // Prepare data for chart
        $labels = $expenses->map(fn ($expense) => Carbon::parse($expense->date)->format('M-d') . ' '.$expense->category->name)->toArray();
        $data = $expenses->map(fn ($expense) => $expense->amount)->toArray();


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
            'total' => $expenses->sum('amount'),
        ];
    }

    public function getHeading(): string
    {
        $total = $this->getData()['total']; // Access total from chart data
        return static::$heading . ' - ' . number_format($total, 2) . ' ' . config('filament.currency.code');
    }

}

