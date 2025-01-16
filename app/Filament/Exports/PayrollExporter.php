<?php

namespace App\Filament\Exports;

use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Support\Facades\Log;

class PayrollExporter extends Exporter
{
    protected static ?string $fileName = 'payroll.csv';

    /**
     * Definiowanie kolumn eksportu.
     */
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')
                ->label('Imię i nazwisko'),

            ExportColumn::make('email')
                ->label('Email'),

            ExportColumn::make('total_hours')
                ->label('Przepracowane godziny')
                ->state(function (User $record) {
                    // Wyznacz początek i koniec aktualnie wybranego miesiąca
                    $startOfMonth = Carbon::now()->startOfMonth();
                    $endOfMonth = Carbon::now()->endOfMonth();

                    // Oblicz łączną liczbę przepracowanych sekund w danym miesiącu
                    $totalSeconds = $record->actionHistories()
                        ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
                        ->sum('elapsed_time');

                    // Przelicz sekundy na godziny, minuty i sekundy
                    $hours = intdiv($totalSeconds, 3600);
                    $minutes = intdiv($totalSeconds % 3600, 60);
                    $seconds = $totalSeconds % 60;

                    // Zwróć w formacie `XXh YYm ZZs`
                    return sprintf('%02dh %02dm %02ds', $hours, $minutes, $seconds);
                }),

            ExportColumn::make('total_salary')
                ->label('Wynagrodzenie (zł)')
                ->state(function (User $record) {
                    // Wyznacz początek i koniec aktualnie wybranego miesiąca
                    $startOfMonth = Carbon::now()->startOfMonth();
                    $endOfMonth = Carbon::now()->endOfMonth();

                    // Oblicz łączną liczbę przepracowanych sekund w danym miesiącu
                    $totalSeconds = $record->actionHistories()
                        ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
                        ->sum('elapsed_time');

                    // Oblicz całkowitą liczbę godzin
                    $totalHours = $totalSeconds / 3600;

                    // Pobierz stawkę godzinową użytkownika
                    $hourlyRate = $record->hourly_rate ?? 0;

                    // Oblicz wynagrodzenie na podstawie przepracowanych godzin
                    $totalSalary = ($hourlyRate / 100) * $totalHours;

                    // Zwróć wynik w formacie `XX,YY zł`
                    return number_format($totalSalary, 2, ',', ' ') . ' zł';
                }),
        ];
    }


    /**
     * Pobieranie danych do eksportu.
     */
    public function getData(HasTable $table): array
    {
        $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

        $users = User::query()->with(['actionHistories' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('start_time', [$startOfMonth, $endOfMonth]);
        }])->get();

        $data = $users->map(function ($user) {
            $totalSeconds = $user->actionHistories->sum('elapsed_time');

            $hours = intdiv($totalSeconds, 3600);
            $minutes = intdiv($totalSeconds % 3600, 60);
            $seconds = $totalSeconds % 60;

            return [
                'name' => $user->name,
                'email' => $user->email,
                'total_hours' => sprintf('%02dh %02dm %02ds', $hours, $minutes, $seconds),
                'total_salary' => $user->hourly_rate ? number_format(($user->hourly_rate / 100) * ($totalSeconds / 3600), 2, ',', ' ') : 'Nie dotyczy',
            ];
        })->toArray();

        Log::info('Eksportowane dane', $data);

        return $data;
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Eksport wynagrodzeń został zakończony. Wyeksportowano ' . number_format($export->successful_rows) . ' ' . str('wiersz')->plural($export->successful_rows) . '.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' Nie udało się wyeksportować ' . number_format($failedRowsCount) . ' ' . str('wiersz')->plural($failedRowsCount) . '.';
        }

        return $body;
    }
}
