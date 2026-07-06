<?php

namespace App\Filament\Resources\TrademarkRegistrations;

use App\Filament\Resources\TrademarkRegistrations\Pages\ListTrademarkRegistrations;
use App\Filament\Resources\TrademarkRegistrations\Pages\PrintTrademarkRegistration;
use App\Filament\Resources\TrademarkRegistrations\Pages\ViewTrademarkRegistration;
use App\Filament\Resources\TrademarkRegistrations\Schemas\TrademarkRegistrationInfolist;
use App\Filament\Resources\TrademarkRegistrations\Tables\TrademarkRegistrationsTable;
use App\Models\TrademarkRegistration;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class TrademarkRegistrationResource extends Resource
{
    protected static ?string $model = TrademarkRegistration::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedDocumentDuplicate;

    protected static ?string $navigationLabel = 'Trademark Registrations';

    protected static ?string $pluralModelLabel = 'Trademark Registrations';

    protected static ?string $modelLabel = 'Trademark Registration';

    protected static ?string $recordTitleAttribute = 'trademark_name';

    public static function infolist(Schema $schema): Schema
    {
        return TrademarkRegistrationInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return TrademarkRegistrationsTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTrademarkRegistrations::route('/'),
            'view' => ViewTrademarkRegistration::route('/{record}'),
            'print' => PrintTrademarkRegistration::route('/{record}/print'),
        ];
    }
}
