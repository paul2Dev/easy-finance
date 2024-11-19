<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ExpenseResource\Pages;
use App\Filament\Resources\ExpenseResource\RelationManagers;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\Filter;
use App\Filament\Exports\ExpenseExporter;
use Filament\Tables\Actions\ExportAction;

class ExpenseResource extends Resource
{
    protected static ?string $model = Expense::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-down-circle';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),

                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name', fn ($query) => $query->expense()) // Only expense categories
                    ->required(),

                Forms\Components\DatePicker::make('date')
                    ->label('Date')
                    ->default(now())
                    ->required(),

                Forms\Components\TextInput::make('amount')
                    ->label('Amount')
                    ->numeric()
                    ->required(),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->nullable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->headerActions([
                ExportAction::make()
                    ->exporter(ExpenseExporter::class)
                    ->label('download export'),
            ])
            ->columns([
                Tables\Columns\TextColumn::make('category.name')
                ->label('Category')
                ->searchable()
                ->sortable(),
                Tables\Columns\TextColumn::make('date')
                ->label('Date')
                ->date()
                ->sortable(),
                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount')
                    ->sortable()
                    ->money(config('filament.currency.code')),
                Tables\Columns\TextColumn::make('description')
                ->label('Description')
                ->searchable()
                ->limit(50),
            ])
            ->defaultSort('id', 'desc')
            ->filters([
                Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From'),
                        Forms\Components\DatePicker::make('to')
                            ->label('To'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['from'],
                                fn ($query, $date) => $query->where('date', '>=', $date)
                            )
                            ->when(
                                $data['to'],
                                fn ($query, $date) => $query->where('date', '<=', $date)
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListExpenses::route('/'),
            'create' => Pages\CreateExpense::route('/create'),
            'edit' => Pages\EditExpense::route('/{record}/edit'),
        ];
    }
}
