<?php

namespace App\Filament\Resources\TrademarkRegistrations\Pages;

use App\Filament\Resources\TrademarkRegistrations\TrademarkRegistrationResource;
use Filament\Resources\Pages\ListRecords;

class ListTrademarkRegistrations extends ListRecords
{
    protected static string $resource = TrademarkRegistrationResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
