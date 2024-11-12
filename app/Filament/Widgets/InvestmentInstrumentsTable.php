<?php

namespace App\Filament\Widgets;

use App\Services\YahooFinanceService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\InvestmentInstrument;
use App\Models\Investment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;


class InvestmentInstrumentsTable extends BaseWidget
{
    protected static bool $isDiscovered = false;

    protected static ?string $heading = 'Investment Instruments Overview';

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                InvestmentInstrument::query()
                // Eager load investments to avoid N+1 query issues
                ->with(['investments' => function ($query) {
                    $query->select('investment_instrument_id', 'quantity', 'price', 'total');
                }])
                ->select([
                    'investment_instruments.*',
                    DB::raw('SUM(investments.total) AS total_invested'),
                    DB::raw('SUM(investments.quantity) AS total_units'),
                    DB::raw('AVG(investments.price) AS dca_price'),
                ])
                ->leftJoin('investments', 'investments.investment_instrument_id', '=', 'investment_instruments.id')
                ->groupBy('investment_instruments.id') // Group by investment instrument to aggregate data
            )
            ->columns([
                // Instrument Name Column
                Tables\Columns\TextColumn::make('name')
                    ->label('Instrument')
                    ->sortable(),

                // Total Invested Column
                Tables\Columns\TextColumn::make('total_invested')
                    ->label('Total Invested')
                    ->sortable()
                    ->money(config('filament.investment_currency.code'))
                    ->badge(),

                // DCA Price Column
                Tables\Columns\TextColumn::make('dca_price')
                    ->label('DCA Price')
                    ->sortable()
                    ->money(config('filament.investment_currency.code'))
                    ->color('info')
                    ->badge(),

                // Actual Price - Placeholder for actual price logic
                Tables\Columns\TextColumn::make('price')
                    ->label('Actual Price')
                    ->sortable()
                    ->money(config('filament.investment_currency.code'))
                    ->color('primary')
                    ->badge(),

                // Profit Column (Calculated field based on current price)
                Tables\Columns\TextColumn::make('profit')
                    ->label('Profit')
                    ->sortable()
                    ->money(config('filament.investment_currency.code'))
                    ->getStateUsing(function ($record) {
                        $actualPrice = $record->price;
                        $totalValue = $record->total_units * $actualPrice;
                        return $totalValue - $record->total_invested;
                    })
                    ->color(
                        // Color the total units based on the value
                        function ($record) {
                            $actualPrice = $record->price;
                            $totalValue = $record->total_units * $actualPrice;
                            return $totalValue - $record->total_invested > 0 ? 'success' : 'danger';
                        }
                    )
                    ->badge(),

                // Total Units Column
                Tables\Columns\TextColumn::make('total_units')
                    ->label('Total Units')
                    ->sortable()
                    ->color('gray')
                    ->badge(),
            ]);
    }
}
