<?php

namespace App\Filament\Resources\DriverLicenses\Pages;

use App\Filament\Resources\DriverLicenses\DriverLicenseResource;
use Filament\Resources\Pages\ListRecords;

class ListDriverLicenses extends ListRecords
{
    protected static string $resource = DriverLicenseResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
