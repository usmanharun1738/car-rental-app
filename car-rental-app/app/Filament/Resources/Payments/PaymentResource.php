<?php

namespace App\Filament\Resources\Payments;

use App\Filament\Resources\Payments\Pages\CreatePayment;
use App\Filament\Resources\Payments\Pages\EditPayment;
use App\Filament\Resources\Payments\Pages\ListPayments;
use App\Filament\Resources\Payments\Pages\ViewPayment;
use App\Filament\Resources\Payments\Schemas\PaymentForm;
use App\Filament\Resources\Payments\Tables\PaymentsTable;
use App\Models\Payment;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class PaymentResource extends Resource
{
    protected static ?string $model = Payment::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCreditCard;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'Payment';

    public static function form(Schema $schema): Schema
    {
        return PaymentForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PaymentsTable::configure($table);
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->components([
                \Filament\Infolists\Components\TextEntry::make('booking.confirmation_code')
                    ->label('Booking Code')
                    ->copyable(),
                \Filament\Infolists\Components\TextEntry::make('booking.user.name')
                    ->label('Customer'),
                \Filament\Infolists\Components\TextEntry::make('booking.vehicle.full_name')
                    ->label('Vehicle')
                    ->getStateUsing(fn (Payment $record) => "{$record->booking->vehicle->make} {$record->booking->vehicle->model}"),
                \Filament\Infolists\Components\TextEntry::make('amount')
                    ->label('Amount')
                    ->money('NGN'),
                \Filament\Infolists\Components\TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                \Filament\Infolists\Components\TextEntry::make('payment_method')
                    ->label('Payment Method'),
                \Filament\Infolists\Components\TextEntry::make('transaction_reference')
                    ->label('Transaction Reference')
                    ->copyable()
                    ->fontFamily('mono'),
                \Filament\Infolists\Components\TextEntry::make('provider_reference')
                    ->label('Provider Reference')
                    ->copyable()
                    ->fontFamily('mono'),
                \Filament\Infolists\Components\TextEntry::make('paid_at')
                    ->label('Paid At')
                    ->dateTime('M d, Y H:i'),
                \Filament\Infolists\Components\TextEntry::make('created_at')
                    ->label('Created At')
                    ->dateTime('M d, Y H:i'),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPayments::route('/'),
            'create' => CreatePayment::route('/create'),
            'view' => ViewPayment::route('/{record}'),
            'edit' => EditPayment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', \App\Enums\PaymentStatus::FAILED)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return static::getModel()::where('status', \App\Enums\PaymentStatus::FAILED)->count() > 0 ? 'danger' : 'success';
    }
}
