<?php

namespace App\Filament\Resources\InvestmentInstrumentResource\Pages;

use App\Filament\Resources\InvestmentInstrumentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateInvestmentInstrument extends CreateRecord
{
    protected static string $resource = InvestmentInstrumentResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
