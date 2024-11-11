<?php

namespace App\Filament\Resources\InvestmentInstrumentResource\Pages;

use App\Filament\Resources\InvestmentInstrumentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListInvestmentInstruments extends ListRecords
{
    protected static string $resource = InvestmentInstrumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
