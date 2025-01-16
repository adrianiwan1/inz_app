<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\UserTableWidget;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;

class Dashboard extends \Filament\Pages\Dashboard
{
//    protected function getHeaderWidgets(): array
//    {
//        return [
//            UserTableWidget::class, // Widget tabeli użytkowników - wyświetlany jako pierwszy
//        ];
//    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Resources\DashboardResource\Widgets\UserTableWidget::class,
        ];
    }
}
