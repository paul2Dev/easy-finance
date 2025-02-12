<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BillResource\Pages;
use App\Models\Bill;
use App\Models\Expense;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Actions\Action;

class BillResource extends Resource
{
    protected static ?string $model = Bill::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(Auth::id()),

                Forms\Components\Select::make('category_id')
                    ->label('Category')
                    ->relationship('category', 'name')
                    ->required(),

                Forms\Components\TextInput::make('name')
                    ->label('Bill Name')
                    ->required(),

                    Forms\Components\TextInput::make('description')
                    ->label('Description'),

                Forms\Components\TextInput::make('amount')
                    ->label('Amount')
                    ->numeric(),

                Forms\Components\Toggle::make('is_fixed')
                    ->label('Fixed Amount?')
                    ->default(true),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                    ])
                    ->default('pending'),
            ]);
    }

    public static function table(Table $table): Table
    {

        \App\Models\Bill::resetPendingStatus(); // Reset status when accessing the table

        return $table
            ->defaultPaginationPageOption(25)
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Bill Name')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Category')
                    ->sortable(),

                Tables\Columns\TextInputColumn::make('amount')
                    ->label('Amount')
                    ->rules(['numeric', 'min:0'])
                    ->extraAttributes(['class' => 'text-right']) // Optional: Align numbers to the right
                    ->disabled(fn ($record) => $record->is_fixed), // Disable for fixed bills

                Tables\Columns\TextInputColumn::make('Description')
                    ->label('Description')
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->formatStateUsing(fn ($state) => ucfirst($state)) // Capitalizes the status
                    ->color(fn ($state) => match ($state) {
                        'pending' => 'danger',
                        'paid' => 'success',
                    })
                    ->badge(),
                
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),

                // Pay bill action
                Action::make('Pay Bill')
                    ->visible(fn ($record) => $record->status === 'pending')
                    ->action(fn ($record) => $record->update(['status' => 'paid']))
                    ->after(function ($record) {
                        Expense::create([
                            'user_id' => Auth::id(),
                            'category_id' => $record->category_id,
                            'date' => now(),
                            'amount' => $record->amount,
                            'description' => 'Plata factura - ' . $record->name . ' - ' . $record->description,
                        ]);
                    }),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ])
            ->defaultSort('status', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBills::route('/'),
            //'create' => Pages\CreateBill::route('/create'),
            //'edit' => Pages\EditBill::route('/{record}/edit'),
        ];
    }
}

