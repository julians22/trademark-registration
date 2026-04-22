<?php

namespace App\Filament\Resources\Registrations\Schemas;

use Dom\Text;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Infolists\Components\RepeatableEntry;

class RegistrationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Registrant Information')
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email')
                            ->label('Email address'),
                        TextEntry::make('phone')
                            ->placeholder('-'),
                        TextEntry::make('company')
                            ->placeholder('-'),
                        TextEntry::make('whatsapp')
                            ->placeholder('-'),
                        TextEntry::make('wechat')
                            ->placeholder('-'),
                        TextEntry::make('created_at')
                            ->dateTime()
                            ->placeholder('-'),
                        TextEntry::make('updated_at')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
                RepeatableEntry::make('details')
                    ->label('Registration Details')
                    ->schema([
                        TextEntry::make('word_marks')
                            ->label('Word Marks')
                            ->placeholder('-'),
                        ImageEntry::make('logo')
                            ->label('Logo')
                            ->disk('public')
                            ->placeholder('-'),
                        TextEntry::make('logo')
                            ->label('Logo URL')
                            ->placeholder('-'),
                        TextEntry::make('classifications')
                            ->label('Classifications')
                            ->placeholder('-'),
                        TextEntry::make('goods_services')
                            ->label('Goods/Services')
                            ->placeholder('-'),
                        TextEntry::make('currency')
                            ->label('Currency')
                            ->placeholder('-'),
                        TextEntry::make('trademark_administration')
                            ->label('Trademark Administration Type')
                            ->placeholder('-'),
                        TextEntry::make('countries')
                            ->label('Countries')
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
