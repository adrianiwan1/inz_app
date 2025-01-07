<?php

namespace App\Filament\Widgets;

use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;

class UserTableWidget extends BaseWidget
{
    protected static ?string $heading = 'Lista użytkowników'; // Tytuł widgetu

    protected function getTableQuery(): Builder
    {
        return User::query(); // Zapytanie zwracające wszystkich użytkowników
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Imię i nazwisko')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('email')
                ->label('E-mail')
                ->sortable()
                ->searchable(),

            Tables\Columns\TextColumn::make('created_at')
                ->label('Data utworzenia konta')
                ->dateTime('Y-m-d H:i')
                ->sortable(),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        return true; // Włącz paginację
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
