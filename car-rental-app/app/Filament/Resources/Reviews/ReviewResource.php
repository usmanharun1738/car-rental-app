<?php

namespace App\Filament\Resources\Reviews;

use App\Filament\Resources\Reviews\Pages\ListReviews;
use App\Filament\Resources\Reviews\Pages\ViewReview;
use App\Models\Review;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Builder;

class ReviewResource extends Resource
{
    protected static ?string $model = Review::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedStar;

    protected static ?int $navigationSort = 10;

    protected static ?string $recordTitleAttribute = 'title';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('vehicle.full_name')
                    ->label('Vehicle')
                    ->getStateUsing(fn (Review $record) => "{$record->vehicle->make} {$record->vehicle->model}")
                    ->searchable(['make', 'model']),
                TextColumn::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state) . str_repeat('☆', 5 - $state))
                    ->color('warning')
                    ->sortable(),
                TextColumn::make('title')
                    ->limit(30)
                    ->searchable(),
                IconColumn::make('is_approved')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning'),
                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M d, Y')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_approved')
                    ->label('Approval Status')
                    ->placeholder('All Reviews')
                    ->trueLabel('Approved')
                    ->falseLabel('Pending'),
                SelectFilter::make('rating')
                    ->options([
                        5 => '5 Stars',
                        4 => '4 Stars',
                        3 => '3 Stars',
                        2 => '2 Stars',
                        1 => '1 Star',
                    ]),
            ])
            ->actions([
                \Filament\Actions\ViewAction::make(),
                \Filament\Actions\Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-o-check')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn (Review $record) => $record->is_approved)
                    ->action(function (Review $record) {
                        $record->approve(auth()->user());
                        Notification::make()
                            ->title('Review approved')
                            ->success()
                            ->send();
                    }),
                \Filament\Actions\Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject review')
                    ->modalDescription('Are you sure you want to reject and delete this review?')
                    ->action(function (Review $record) {
                        $record->reject();
                        Notification::make()
                            ->title('Review rejected and deleted')
                            ->warning()
                            ->send();
                    }),
                \Filament\Actions\Action::make('respond')
                    ->label('Respond')
                    ->icon('heroicon-o-chat-bubble-left-right')
                    ->color('info')
                    ->form([
                        Textarea::make('admin_response')
                            ->label('Admin Response')
                            ->placeholder('Write a response to this review...')
                            ->required()
                            ->rows(4),
                    ])
                    ->fillForm(fn (Review $record) => ['admin_response' => $record->admin_response])
                    ->action(function (Review $record, array $data) {
                        $record->update(['admin_response' => $data['admin_response']]);
                        Notification::make()
                            ->title('Response saved')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                \Filament\Actions\BulkActionGroup::make([
                    \Filament\Actions\BulkAction::make('approve_selected')
                        ->label('Approve Selected')
                        ->icon('heroicon-o-check')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function (\Illuminate\Database\Eloquent\Collection $records) {
                            $records->each(fn (Review $record) => $record->approve(auth()->user()));
                            Notification::make()
                                ->title('Selected reviews approved')
                                ->success()
                                ->send();
                        }),
                    \Filament\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->components([
                \Filament\Infolists\Components\TextEntry::make('user.name')
                    ->label('Customer'),
                \Filament\Infolists\Components\TextEntry::make('user.email')
                    ->label('Customer Email')
                    ->copyable(),
                \Filament\Infolists\Components\TextEntry::make('vehicle_info')
                    ->label('Vehicle')
                    ->getStateUsing(fn (Review $record) => "{$record->vehicle->make} {$record->vehicle->model} ({$record->vehicle->year})"),
                \Filament\Infolists\Components\TextEntry::make('booking.confirmation_code')
                    ->label('Booking Confirmation')
                    ->copyable(),
                \Filament\Infolists\Components\TextEntry::make('rating')
                    ->label('Rating')
                    ->formatStateUsing(fn (int $state): string => str_repeat('★', $state) . str_repeat('☆', 5 - $state) . " ({$state}/5)"),
                \Filament\Infolists\Components\TextEntry::make('title')
                    ->label('Review Title'),
                \Filament\Infolists\Components\TextEntry::make('comment')
                    ->label('Review Comment')
                    ->columnSpanFull(),
                \Filament\Infolists\Components\TextEntry::make('is_approved')
                    ->label('Status')
                    ->formatStateUsing(fn (bool $state) => $state ? 'Approved' : 'Pending')
                    ->badge()
                    ->color(fn (bool $state) => $state ? 'success' : 'warning'),
                \Filament\Infolists\Components\TextEntry::make('approved_at')
                    ->label('Approved At')
                    ->dateTime('M d, Y H:i')
                    ->placeholder('Not yet approved'),
                \Filament\Infolists\Components\TextEntry::make('approver.name')
                    ->label('Approved By')
                    ->placeholder('N/A'),
                \Filament\Infolists\Components\TextEntry::make('admin_response')
                    ->label('Admin Response')
                    ->placeholder('No response yet')
                    ->columnSpanFull(),
                \Filament\Infolists\Components\TextEntry::make('created_at')
                    ->label('Submitted At')
                    ->dateTime('M d, Y H:i'),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListReviews::route('/'),
            'view' => ViewReview::route('/{record}'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('is_approved', false)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return static::getModel()::where('is_approved', false)->count() > 0 ? 'warning' : 'success';
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['user', 'vehicle', 'booking', 'approver']);
    }
}
