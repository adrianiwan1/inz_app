<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class AdminDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home'; // Ikona w menu
    protected static ?string $navigationLabel = 'cos co bedzie'; // Etykieta w menu
    protected static ?string $navigationGroup = 'Panel Administratora'; // Grupa w menu
    protected static string $view = 'filament.pages.admin-dashboard'; // Widok

    public function getWidgets(): array
    {
        return [
            \Filament\Widgets\AccountWidget::class,
            \Filament\Widgets\FilamentInfoWidget::class,
        ];
    }
}
