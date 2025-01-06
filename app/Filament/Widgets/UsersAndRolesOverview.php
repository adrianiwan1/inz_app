<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class UsersAndRolesOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Liczba kont', User::query()->count()),
            Stat::make('liczba pracowników', User::role('employee')->count()),
            Stat::make('Liczba administratorów', User::role('admin')->count()),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasRole('admin');
    }
}
