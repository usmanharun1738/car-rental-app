<?php

namespace App\Filament\Resources\Vehicles;

use App\Filament\Resources\Vehicles\Pages\CreateVehicle;
use App\Filament\Resources\Vehicles\Pages\EditVehicle;
use App\Filament\Resources\Vehicles\Pages\ListVehicles;
use App\Filament\Resources\Vehicles\Pages\ViewVehicle;
use App\Filament\Resources\Vehicles\Schemas\VehicleForm;
use App\Filament\Resources\Vehicles\Tables\VehiclesTable;
use App\Models\Vehicle;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class VehicleResource extends Resource
{
    protected static ?string $model = Vehicle::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedTruck;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'make';

    public static function form(Schema $schema): Schema
    {
        return VehicleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return VehiclesTable::configure($table);
    }

    public static function infolist(Schema $infolist): Schema
    {
        return $infolist
            ->components([
                \Filament\Infolists\Components\ImageEntry::make('primary_image_url')
                    ->label('Image')
                    ->circular()
                    ->size(100),
                \Filament\Infolists\Components\TextEntry::make('make')
                    ->label('Make'),
                \Filament\Infolists\Components\TextEntry::make('model')
                    ->label('Model'),
                \Filament\Infolists\Components\TextEntry::make('year')
                    ->label('Year'),
                \Filament\Infolists\Components\TextEntry::make('color')
                    ->label('Color'),
                \Filament\Infolists\Components\TextEntry::make('license_plate')
                    ->label('License Plate')
                    ->copyable(),
                \Filament\Infolists\Components\TextEntry::make('daily_rate')
                    ->label('Daily Rate')
                    ->money('NGN'),
                \Filament\Infolists\Components\TextEntry::make('status')
                    ->label('Status')
                    ->badge(),
                \Filament\Infolists\Components\TextEntry::make('transmission')
                    ->label('Transmission'),
                \Filament\Infolists\Components\TextEntry::make('fuel_type')
                    ->label('Fuel Type'),
                \Filament\Infolists\Components\TextEntry::make('seats')
                    ->label('Seats'),
                \Filament\Infolists\Components\TextEntry::make('mileage')
                    ->label('Mileage'),
                \Filament\Infolists\Components\TextEntry::make('location')
                    ->label('Location'),
                \Filament\Infolists\Components\TextEntry::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
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
            'index' => ListVehicles::route('/'),
            'create' => CreateVehicle::route('/create'),
            'view' => ViewVehicle::route('/{record}'),
            'edit' => EditVehicle::route('/{record}/edit'),
        ];
    }
}
