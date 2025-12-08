<?php

namespace App\Filament\Resources\Payments\Schemas;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PaymentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Payment Details')
                    ->columns(2)
                    ->schema([
                        TextInput::make('amount')
                            ->required()
                            ->numeric()
                            ->prefix('₦'),
                        Select::make('method')
                            ->options(PaymentMethod::class)
                            ->required(),
                        Select::make('status')
                            ->options(PaymentStatus::class)
                            ->default('pending')
                            ->required(),
                        TextInput::make('transaction_reference'),
                    ]),

                \Filament\Forms\Components\Section::make('Proof of Payment')
                    ->schema([
                        \Filament\Forms\Components\FileUpload::make('proof_url')
                            ->image()
                            ->directory('payments')
                            ->openable(),
                    ]),

                \Filament\Forms\Components\Section::make('Linked Entity')
                    ->description('Read-only. This payment is linked to the entity below.')
                    ->schema([
                        TextInput::make('payable_type')
                            ->disabled()
                            ->dehydrated(false),
                        TextInput::make('payable_id')
                            ->disabled()
                            ->dehydrated(false),
                    ]),
            ]);
    }
}
