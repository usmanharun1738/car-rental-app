<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Enums\VehicleStatus;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('make')
                    ->required(),
                TextInput::make('model')
                    ->required(),
                TextInput::make('year')
                    ->required()
                    ->numeric(),
                TextInput::make('plate_number')
                    ->required(),
                TextInput::make('daily_rate')
                    ->required()
                    ->numeric()
                    ->prefix('₦'),
                Select::make('status')
                    ->options(VehicleStatus::class)
                    ->default('available')
                    ->required(),
                FileUpload::make('image_url')
                    ->image(),
                Textarea::make('features')
                    ->default(null)
                    ->columnSpanFull(),
            ]);
    }
}
