<?php

namespace App\Filament\Resources\Reviews\Pages;

use App\Filament\Resources\Reviews\ReviewResource;
use App\Models\Review;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Textarea;

class ViewReview extends ViewRecord
{
    protected static string $resource = ReviewResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('approve')
                ->label('Approve Review')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->hidden(fn () => $this->record->is_approved)
                ->action(function () {
                    $this->record->approve(auth()->user());
                    Notification::make()
                        ->title('Review approved successfully')
                        ->success()
                        ->send();
                    $this->refreshFormData(['is_approved', 'approved_at', 'approved_by']);
                }),
            Actions\Action::make('reject')
                ->label('Reject Review')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->modalHeading('Reject and delete review?')
                ->modalDescription('This will permanently delete this review. This action cannot be undone.')
                ->action(function () {
                    $this->record->reject();
                    Notification::make()
                        ->title('Review rejected and deleted')
                        ->warning()
                        ->send();
                    $this->redirect(ReviewResource::getUrl('index'));
                }),
            Actions\Action::make('respond')
                ->label('Add/Edit Response')
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('info')
                ->form([
                    Textarea::make('admin_response')
                        ->label('Admin Response')
                        ->placeholder('Write a response to this review that will be publicly visible...')
                        ->required()
                        ->rows(4),
                ])
                ->fillForm(fn () => ['admin_response' => $this->record->admin_response])
                ->action(function (array $data) {
                    $this->record->update(['admin_response' => $data['admin_response']]);
                    Notification::make()
                        ->title('Response saved successfully')
                        ->success()
                        ->send();
                    $this->refreshFormData(['admin_response']);
                }),
        ];
    }
}
