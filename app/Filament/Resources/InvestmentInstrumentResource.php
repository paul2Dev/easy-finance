<?php

namespace App\Filament\Resources;

use App\Filament\Resources\InvestmentInstrumentResource\Pages;
use App\Filament\Resources\InvestmentInstrumentResource\RelationManagers;
use App\Models\InvestmentInstrument;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;

class InvestmentInstrumentResource extends Resource
{
    protected static ?string $model = InvestmentInstrument::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),

                Forms\Components\TextInput::make('name')
                    ->required()
                    ->label('Name'),

                Forms\Components\Select::make('type')
                    ->options([
                        'etf' => 'ETF',
                        'crypto' => 'Crypto',
                        'stocks' => 'Stocks',
                    ])
                    ->required()
                    ->label('Type'),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->nullable(),

                Forms\Components\TextInput::make('ticker')
                    ->placeholder('Enter Yahoo Finance ticker, e.g., IWDA.AS')
                    ->required(), // Add ticker input field,
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('type')
                    ->sortable(),

                Tables\Columns\TextColumn::make('description')
                    ->limit(50),

                Tables\Columns\TextColumn::make('ticker')
                    ->sortable(),
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
            'index' => Pages\ListInvestmentInstruments::route('/'),
            //'create' => Pages\CreateInvestmentInstrument::route('/create'),
            //'edit' => Pages\EditInvestmentInstrument::route('/{record}/edit'),
        ];
    }
}
