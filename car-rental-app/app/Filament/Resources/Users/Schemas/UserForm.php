<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Personal Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required(),
                        TextInput::make('email')
                            ->email()
                            ->required(),
                        TextInput::make('phone')
                            ->tel(),
                        Textarea::make('address')
                            ->columnSpanFull(),
                    ]),

                \Filament\Forms\Components\Section::make('ID Verification')
                    ->columns(2)
                    ->schema([
                        TextInput::make('license_number'),
                        FileUpload::make('license_image_url')
                            ->image()
                            ->directory('licenses'),
                    ]),

                \Filament\Forms\Components\Section::make('Security')
                    ->schema([
                        TextInput::make('password')
                            ->password()
                            ->dehydrated(fn ($state) => filled($state))
                            ->required(fn (string $context): bool => $context === 'create'),
                    ]),
            ]);
    }
}
