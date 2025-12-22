<?php

namespace App\Filament\Resources\DriverLicenses\Pages;

use App\Enums\LicenseStatus;
use App\Filament\Resources\DriverLicenses\DriverLicenseResource;
use Filament\Actions\Action;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewDriverLicense extends ViewRecord
{
    protected static string $resource = DriverLicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('verify')
                ->label('Verify License')
                ->icon('heroicon-o-check-badge')
                ->color('success')
                ->visible(fn () => $this->record->status === LicenseStatus::PENDING)
                ->requiresConfirmation()
                ->modalHeading('Verify License')
                ->modalDescription('Are you sure you want to verify this driver\'s license?')
                ->action(function () {
                    $this->record->verify(auth()->user());
                    Notification::make()
                        ->title('License Verified Successfully')
                        ->success()
                        ->send();
                }),

            Action::make('reject')
                ->label('Reject License')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->visible(fn () => $this->record->status === LicenseStatus::PENDING)
                ->form([
                    Textarea::make('rejection_reason')
                        ->label('Reason for Rejection')
                        ->required()
                        ->placeholder('Explain why this license is being rejected...'),
                ])
                ->action(function (array $data) {
                    $this->record->reject(auth()->user(), $data['rejection_reason']);
                    Notification::make()
                        ->title('License Rejected')
                        ->warning()
                        ->send();
                }),
        ];
    }
}
