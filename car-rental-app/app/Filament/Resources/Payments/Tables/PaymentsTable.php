<?php

namespace App\Filament\Resources\Payments\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PaymentsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payable_type')
                    ->searchable(),
                TextColumn::make('payable_id')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('amount')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('method')
                    ->badge()
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->searchable(),
                TextColumn::make('transaction_reference')
                    ->searchable(),
                TextColumn::make('proof_url')
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
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
