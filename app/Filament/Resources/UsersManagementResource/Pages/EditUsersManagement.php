<?php

namespace App\Filament\Resources\UsersManagementResource\Pages;

use App\Filament\Resources\UsersManagementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsersManagement extends EditRecord
{
    protected static string $resource = UsersManagementResource::class;

    protected function getHeaderActions(): array
    {
        return [
//            Actions\DeleteAction::make(),
        ];
    }
}
