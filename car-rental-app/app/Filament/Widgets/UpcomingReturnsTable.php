<?php

namespace App\Filament\Widgets;

use App\Enums\BookingStatus;
use App\Models\Booking;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Carbon;

class UpcomingReturnsTable extends BaseWidget
{
    protected static ?string $heading = 'Upcoming Returns (48h)';

    protected static ?int $sort = 4;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Booking::query()
                    ->whereIn('status', [BookingStatus::CONFIRMED, BookingStatus::ACTIVE])
                    ->whereBetween('end_time', [
                        Carbon::now(),
                        Carbon::now()->addHours(48),
                    ])
                    ->orderBy('end_time')
            )
            ->columns([
                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('vehicle.plate_number')
                    ->label('Vehicle')
                    ->formatStateUsing(fn (Booking $record) => "{$record->vehicle->make} {$record->vehicle->model} ({$record->vehicle->plate_number})"),
                TextColumn::make('end_time')
                    ->label('Return Date')
                    ->dateTime('M d, Y H:i')
                    ->sortable(),
                TextColumn::make('time_remaining')
                    ->label('Time Left')
                    ->getStateUsing(function (Booking $record) {
                        $now = Carbon::now();
                        $end = Carbon::parse($record->end_time);
                        
                        if ($end->isPast()) {
                            return 'Overdue';
                        }
                        
                        return $end->diffForHumans($now, ['parts' => 2, 'short' => true]);
                    })
                    ->badge()
                    ->color(function (Booking $record) {
                        $hoursLeft = Carbon::now()->diffInHours(Carbon::parse($record->end_time), false);
                        
                        if ($hoursLeft < 0) return 'danger';
                        if ($hoursLeft < 6) return 'warning';
                        return 'success';
                    }),
                TextColumn::make('status')
                    ->badge(),
            ])
            ->paginated(false)
            ->emptyStateHeading('No upcoming returns')
            ->emptyStateDescription('No vehicles are due for return in the next 48 hours.');
    }
}
