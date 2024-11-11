<?php

namespace App\Filament\Resources\InvestmentInstrumentResource\Pages;

use App\Filament\Resources\InvestmentInstrumentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditInvestmentInstrument extends EditRecord
{
    protected static string $resource = InvestmentInstrumentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
