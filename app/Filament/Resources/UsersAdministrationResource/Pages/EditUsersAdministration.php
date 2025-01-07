<?php

namespace App\Filament\Resources\UsersAdministrationResource\Pages;

use App\Filament\Resources\UsersAdministrationResource;
use Filament\Resources\Pages\EditRecord;
use Spatie\Permission\Models\Role;

class EditUsersAdministration extends EditRecord
{
    protected static string $resource = UsersAdministrationResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $user = $this->record;

        // Usuń wszystkie role użytkownika
        $user->syncRoles([]);

        // Dodaj nową rolę na podstawie ID
        $role = Role::find($data['roles']); // Pobierz rolę na podstawie ID
        if ($role) {
            $user->assignRole($role->name);
        }

        // Usuń klucz 'roles', aby nie próbować go zapisać bezpośrednio
        unset($data['roles']);

        return $data;
    }
}
