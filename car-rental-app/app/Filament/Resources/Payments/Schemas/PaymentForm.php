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
                TextInput::make('payable_type')
                    ->required(),
                TextInput::make('payable_id')
                    ->required()
                    ->numeric(),
                TextInput::make('amount')
                    ->required()
                    ->numeric(),
                Select::make('method')
                    ->options(PaymentMethod::class)
                    ->required(),
                Select::make('status')
                    ->options(PaymentStatus::class)
                    ->default('pending')
                    ->required(),
                TextInput::make('transaction_reference')
                    ->default(null),
                TextInput::make('proof_url')
                    ->url()
                    ->default(null),
            ]);
    }
}
