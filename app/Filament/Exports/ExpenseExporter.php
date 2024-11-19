<?php

namespace App\Filament\Exports;

use App\Models\Expense;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ExpenseExporter extends Exporter
{
    protected static ?string $model = Expense::class;

    public static function getColumns(): array
    {
        return [
            //ExportColumn::make('id')->label('ID'),
            //ExportColumn::make('user_id'),
            ExportColumn::make('category.name'),
            ExportColumn::make('date'),
            ExportColumn::make('amount'),
            ExportColumn::make('description'),
            ExportColumn::make('created_at'),
            //ExportColumn::make('updated_at'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your expense export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
