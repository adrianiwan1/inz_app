<?php

namespace App\Filament\Resources\UsersManagementResource\Pages;

use App\Filament\Resources\UsersManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsersManagement extends ListRecords
{
    protected static string $resource = UsersManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\CreateAction::make(),
        ];
    }

    protected function canCreate(): bool
    {
        return false; // Wyłącza przycisk "Utwórz"
    }
}
