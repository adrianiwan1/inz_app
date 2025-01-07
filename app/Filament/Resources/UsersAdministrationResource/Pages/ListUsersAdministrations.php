<?php

namespace App\Filament\Resources\UsersAdministrationResource\Pages;

use App\Filament\Resources\UsersAdministrationResource;
use Filament\Resources\Pages\ListRecords;

class ListUsersAdministration extends ListRecords
{
    protected static string $resource = UsersAdministrationResource::class;

    protected function canCreate(): bool
    {

        return false;
    }
}
