<?php

namespace App\Filament\Resources\Vehicles\Schemas;

use App\Enums\FuelType;
use App\Enums\TransmissionType;
use App\Enums\VehicleStatus;
use App\Models\Feature;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class VehicleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->columns(2)
                    ->schema([
                        TextInput::make('make')
                            ->required()
                            ->placeholder('e.g., Toyota'),
                        TextInput::make('model')
                            ->required()
                            ->placeholder('e.g., Camry'),
                        TextInput::make('year')
                            ->required()
                            ->numeric()
                            ->minValue(1990)
                            ->maxValue(date('Y') + 1)
                            ->placeholder(date('Y')),
                        TextInput::make('plate_number')
                            ->required()
                            ->placeholder('e.g., ABC-123-XY'),
                        TextInput::make('daily_rate')
                            ->required()
                            ->numeric()
                            ->prefix('₦')
                            ->placeholder('85000'),
                        Select::make('status')
                            ->options(VehicleStatus::class)
                            ->default('available')
                            ->required(),
                    ]),

                Section::make('Vehicle Specifications')
                    ->description('Key specifications that help customers decide')
                    ->columns(2)
                    ->schema([
                        Select::make('fuel_type')
                            ->options(FuelType::class)
                            ->required()
                            ->native(false)
                            ->placeholder('Select fuel type'),
                        Select::make('transmission')
                            ->options(TransmissionType::class)
                            ->required()
                            ->native(false)
                            ->placeholder('Select transmission'),
                        TextInput::make('seats')
                            ->required()
                            ->numeric()
                            ->minValue(2)
                            ->maxValue(12)
                            ->default(5)
                            ->suffix('seats'),
                        TextInput::make('mileage')
                            ->numeric()
                            ->minValue(0)
                            ->default(0)
                            ->suffix('km/day')
                            ->helperText('Enter 0 for unlimited mileage'),
                    ]),

                Section::make('Features')
                    ->schema([
                        Select::make('features')
                            ->relationship('features', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->required()
                                    ->unique('features', 'name')
                                    ->placeholder('e.g., Apple CarPlay'),
                            ])
                            ->helperText('Select existing features or create new ones'),
                    ]),

                Section::make('Vehicle Images')
                    ->description('Upload multiple images. The first image or one marked as primary will be shown on listings.')
                    ->schema([
                        Repeater::make('images')
                            ->relationship()
                            ->schema([
                                FileUpload::make('path')
                                    ->label('Image')
                                    ->image()
                                    ->directory('vehicle-images')
                                    ->disk('public')
                                    ->required()
                                    ->imageEditor()
                                    ->columnSpan(2),
                                TextInput::make('alt_text')
                                    ->label('Alt Text')
                                    ->placeholder('e.g., Front view')
                                    ->maxLength(255),
                                Toggle::make('is_primary')
                                    ->label('Primary Image')
                                    ->helperText('Show as main image'),
                            ])
                            ->columns(3)
                            ->reorderable()
                            ->reorderableWithButtons()
                            ->collapsible()
                            ->itemLabel(fn (array $state): ?string => $state['alt_text'] ?? 'Image')
                            ->addActionLabel('Add Image')
                            ->defaultItems(0)
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}
