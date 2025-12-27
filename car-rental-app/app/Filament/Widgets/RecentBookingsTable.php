<?php

namespace App\Filament\Widgets;

use App\Models\Booking;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentBookingsTable extends BaseWidget
{
    protected static ?string $heading = 'Recent Bookings';

    protected static ?int $sort = 5;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->with(['user', 'vehicle'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('confirmation_code')
                    ->label('Code')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->size('sm'),
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('vehicle.plate_number')
                    ->label('Vehicle')
                    ->formatStateUsing(fn (Booking $record) => "{$record->vehicle->make} {$record->vehicle->model}"),
                TextColumn::make('start_time')
                    ->label('Start')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('end_time')
                    ->label('End')
                    ->date('M d, Y')
                    ->sortable(),
                TextColumn::make('total_price')
                    ->label('Total')
                    ->money('NGN')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->paginated(false);
    }
}
