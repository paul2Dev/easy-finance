<?php

namespace App\Filament\Widgets;

use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Grid;
use Filament\Widgets\Widget;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Filament\Notifications\Notification;


class QuickAddTransaction extends Widget implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?int $sort = 2;

    protected static string $view = 'filament.widgets.quick-add-transaction-widget';

    protected static ?string $heading = 'Quick Add Transaction';

    public ?string $type = null;
    public ?int $category_id = null;
    public ?float $amount = null;
    public ?string $date = null;
    public ?string $description = null;

    protected function getFormSchema(): array
    {
        return [
            Grid::make()
                ->schema([
                    Select::make('type')
                        ->label('Transaction Type')
                        ->options([
                            'income' => 'Income',
                            'expense' => 'Expense',
                        ])
                        ->reactive()
                        ->required()
                        ->afterStateUpdated(fn ($state, callable $set) => $set('category_id', null)), // Reset category on type change

                    Select::make('category_id')
                        ->label('Category')
                        ->options(function () {
                            return $this->type === 'income'
                                ? Category::where('type', 'income')->pluck('name', 'id')
                                : Category::where('type', 'expense')->pluck('name', 'id');
                        })
                        ->required(),

                    TextInput::make('amount')
                        ->label('Amount')
                        ->numeric()
                        ->required()
                        ->minValue(0),

                    DatePicker::make('date')
                        ->label('Date')
                        ->default(now())
                        ->required(),

                    TextInput::make('description')
                        ->label('Description')
                        ->columnSpan(2),
                ])
                ->columns(2) // Create two columns
                ->columnSpan(2), // Allow the form to span the entire width
        ];
    }

    public function mount(): void
    {
        $this->form->fill([
            'type' => 'expense', // Default type
            'date' => now(),
        ]);
    }

    public function submit(): void
    {
        $data = $this->form->getState();

        // Validate the form data
        $this->form->validate();

        if ($data['type'] === 'income') {
            Income::create([
                'user_id' => Auth::id(),
                'category_id' => $data['category_id'],
                'amount' => $data['amount'],
                'date' => $data['date'],
                'description' => $data['description'],
            ]);
        } else {
            Expense::create([
                'user_id' => Auth::id(),
                'category_id' => $data['category_id'],
                'amount' => $data['amount'],
                'date' => $data['date'],
                'description' => $data['description'],
            ]);
        }

        // Reset the form fields after submission
        $this->form->fill([
            'type' => 'expense',
            'category_id' => null,
            'amount' => null,
            'date' => now(),
            'description' => null,
        ]);

        // Send a success notification
        Notification::make()
            ->title('Transaction added successfully!')
            ->success()
            ->send();
    }
}
