<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ActionHistory;
use Carbon\Carbon;

class ReportsStatsOverviewWidget extends BaseWidget
{
    protected ?string $heading = 'Podsumowanie Raportów';

    protected function getStats(): array
    {
        $today = Carbon::today();

        return [
            Stat::make(
                'Łączny czas akcji',
                gmdate(
                    'H:i:s',
                    ActionHistory::whereDate('start_time', $today)->sum('elapsed_time')
                )
            ),
            Stat::make(
                'Liczba akcji',
                ActionHistory::whereDate('start_time', $today)->count()
            ),
            Stat::make(
                'Użytkownicy zaangażowani',
                ActionHistory::whereDate('start_time', $today)
                    ->distinct('user_id')
                    ->count('user_id')
            ),
        ];
    }
}
