<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvestmentResource\Pages;
use App\Filament\Resources\InvestmentResource\RelationManagers;
use App\Models\Investment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class InvestmentResource extends Resource
{
    protected static ?string $model = Investment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    public static function form(Form $form): Form
    {
        return $form
        ->schema([
            Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),

            Forms\Components\Select::make('investment_instrument_id')
                ->label('Investment Instrument')
                ->relationship('instrument', 'name')
                ->required(),

            Forms\Components\DatePicker::make('date')
                ->required(),

            Forms\Components\Textarea::make('description')
                ->label('Description')
                ->nullable(),

            Forms\Components\Select::make('type')
                ->options([
                    'buy' => 'Buy',
                    'sell' => 'Sell',
                ])
                ->required(),

            Forms\Components\TextInput::make('quantity')
                ->numeric()
                ->required()
                ->reactive()
                ->debounce(1000)
                //if the quantity field changes, the total field will be updated
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    // Access the 'price' field value from the form state
                    $price = $get('price') ?? 0; // Get the 'price' value from the form state
                    $total = $state * $price; // Calculate total (quantity * price)
                    $set('total', $total); // Set the total field
                }),

            Forms\Components\TextInput::make('price')
                ->numeric()
                ->required()
                ->reactive()
                ->debounce(1000)
                //if the price field changes, the total field will be updated
                ->afterStateUpdated(function ($state, callable $set, callable $get) {
                    // Access the 'quantity' field value from the form state
                    $quantity = $get('quantity') ?? 0; // Get the 'quantity' value from the form state
                    $total = $state * $quantity; // Calculate total (quantity * price)
                    $set('total', $total); // Set the total field
                }),


            Forms\Components\TextInput::make('total')
                ->numeric()
                ->disabled() // Prevents manual entry, making it read-only
                ->dehydrated(), // Ensures 'total' is saved in the model

            Forms\Components\TextInput::make('conversion_rate')
                ->label('Conversion Rate')
                ->numeric()
                ->required(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('instrument.name')
                    ->label('Instrument')
                    ->sortable()
                    ->searchable()
                    ->limit(25),

                Tables\Columns\TextColumn::make('instrument.type')
                    ->label('Type')
                    ->sortable()
                    ->searchable()
                    ->colors([
                        'success' => 'etf',
                        'warning' => 'stocks',
                        'danger' => 'crypto',
                    ])
                    ->badge(),

                Tables\Columns\TextColumn::make('date')
                    ->date()
                    ->sortable()
                    ->badge(),

                Tables\Columns\TextColumn::make('type')
                    ->sortable()
                    ->colors([
                        'success' => 'buy', // 'buy' entries will appear in green
                        'danger' => 'sell', // 'sell' entries will appear in red
                    ])
                    ->badge(),

                Tables\Columns\TextColumn::make('quantity')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->sortable()
                    ->money(config('filament.investment_currency.code')),

                Tables\Columns\TextColumn::make('total')
                    ->sortable()
                    ->money(config('filament.investment_currency.code'))
                    ->badge(),

                Tables\Columns\TextColumn::make('conversion_rate')
                    ->label('Conversion Rate')
                    ->sortable()
                    ->money(config('filament.currency.code')),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListInvestments::route('/'),
            'create' => Pages\CreateInvestment::route('/create'),
            'edit' => Pages\EditInvestment::route('/{record}/edit'),
        ];
    }
}
