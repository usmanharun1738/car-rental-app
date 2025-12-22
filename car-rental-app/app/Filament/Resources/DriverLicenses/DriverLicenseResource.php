<?php

namespace App\Filament\Resources\DriverLicenses;

use App\Enums\LicenseStatus;
use App\Filament\Resources\DriverLicenses\Pages\ListDriverLicenses;
use App\Filament\Resources\DriverLicenses\Pages\ViewDriverLicense;
use App\Models\DriverLicense;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;

class DriverLicenseResource extends Resource
{
    protected static ?string $model = DriverLicense::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedIdentification;

    protected static ?string $recordTitleAttribute = 'license_number';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('license_number')
                    ->label('License #')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono'),
                TextColumn::make('full_name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('expiry_date')
                    ->date()
                    ->sortable()
                    ->color(fn (DriverLicense $record) => $record->isExpired() ? 'danger' : null),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (LicenseStatus $state) => $state->label())
                    ->color(fn (LicenseStatus $state) => $state->color()),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(collect(LicenseStatus::cases())->mapWithKeys(
                        fn ($status) => [$status->value => $status->label()]
                    )),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('verify')
                    ->label('Verify')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->visible(fn (DriverLicense $record) => $record->status === LicenseStatus::PENDING)
                    ->requiresConfirmation()
                    ->modalHeading('Verify License')
                    ->modalDescription('Are you sure you want to verify this driver\'s license? The user will be able to make bookings.')
                    ->action(function (DriverLicense $record) {
                        $record->verify(auth()->user());
                        Notification::make()
                            ->title('License Verified')
                            ->success()
                            ->send();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (DriverLicense $record) => $record->status === LicenseStatus::PENDING)
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Reason for Rejection')
                            ->required()
                            ->placeholder('Please explain why this license is being rejected...'),
                    ])
                    ->action(function (DriverLicense $record, array $data) {
                        $record->reject(auth()->user(), $data['rejection_reason']);
                        Notification::make()
                            ->title('License Rejected')
                            ->warning()
                            ->send();
                    }),
            ])
            ->bulkActions([]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListDriverLicenses::route('/'),
            'view' => ViewDriverLicense::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', LicenseStatus::PENDING)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }
}
