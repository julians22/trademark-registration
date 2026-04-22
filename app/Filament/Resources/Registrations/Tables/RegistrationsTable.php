<?php

namespace App\Filament\Resources\Registrations\Tables;

use App\Mail\RegistationCreated;
use App\Mail\RegistationCreatedUser;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class RegistrationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Email address')
                    ->searchable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('company')
                    ->searchable(),
                TextColumn::make('whatsapp')
                    ->searchable(),
                TextColumn::make('wechat')
                    ->searchable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                Action::make('send_email')
                    ->label('Send Email')
                    ->action(function ($record) {
                        try {
                            Mail::to($record->email)->send(new RegistationCreatedUser($record));
                            Mail::to('dabnerjulian@gmail.com')->send(new RegistationCreated($record));
                            Log::info('Email sent successfully to ' . $record->email);
                            Log::info('Email sent successfully to admin for registration ID ' . $record->id);
                        } catch (\Throwable $th) {
                            Log::error('Failed to send email to ' . $record->email, [
                                'error' => $th->getMessage(),
                                'registration_id' => $record->id,
                            ]);
                        }
                    }),
                // EditAction::make(),
            ])
            ->toolbarActions([
                // BulkActionGroup::make([
                //     DeleteBulkAction::make(),
                // ]),
            ]);
    }
}
