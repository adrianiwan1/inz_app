<?php

namespace App\Filament\Exports;

use App\Models\User;
use Carbon\Carbon;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class PayrollExporter extends Exporter
{
    protected static ?string $fileName = 'payroll.csv';

    /**
     * Określenie kolumn eksportu.
     *
     * @return array
     */
    public static function getColumns(): array
    {
        return [
            ExportColumn::make('name')->label('Imię i nazwisko'),
            ExportColumn::make('email')->label('Email'),
            ExportColumn::make('total_hours')->label('Przepracowane godziny'),
            ExportColumn::make('total_salary')->label('Wynagrodzenie (zł)'),
        ];
    }

    /**
     * Eksportowanie danych na podstawie parametrów.
     *
     * @param array $parameters
     * @return string
     */
    public function export(array $parameters): string
    {
        $selectedYear = $parameters['selectedYear'] ?? now()->year;
        $selectedMonth = $parameters['selectedMonth'] ?? now()->month;

        // Ustaw daty początkową i końcową dla wybranego miesiąca
        $startOfMonth = Carbon::create($selectedYear, $selectedMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::create($selectedYear, $selectedMonth, 1)->endOfMonth();

        // Pobieranie danych użytkowników
        $users = User::with(['actionHistories' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('start_time', [$startOfMonth, $endOfMonth]);
        }])->get();

        // Przygotowanie danych do eksportu
        $rows = $users->map(function ($user) use ($startOfMonth, $endOfMonth) {
            $totalSeconds = $user->actionHistories
                ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
                ->sum('elapsed_time');

            $hours = intdiv($totalSeconds, 3600);
            $minutes = intdiv($totalSeconds % 3600, 60);
            $seconds = $totalSeconds % 60;

            $totalHoursFormatted = sprintf('%02dh %02dm %02ds', $hours, $minutes, $seconds);

            $hourlyRate = $user->hourly_rate ?? 0;
            $totalSalary = ($hourlyRate / 100) * ($totalSeconds / 3600);

            return [
                'name' => $user->name,
                'email' => $user->email,
                'total_hours' => $totalHoursFormatted,
                'total_salary' => $user->employment_type === 'employment'
                    ? 'Nie dotyczy'
                    : number_format($totalSalary, 2, ',', ' '),
            ];
        });

        // Nazwa pliku z dynamicznym miesiącem i rokiem
        $fileName = 'payroll_' . $selectedYear . '_' . str_pad($selectedMonth, 2, '0', STR_PAD_LEFT) . '.csv';

        // Generowanie pliku CSV
        $filePath = storage_path('app/' . $fileName);
        $file = fopen($filePath, 'w');

        // Zapis nagłówków
        fputcsv($file, array_keys($rows->first()));

        // Zapis wierszy
        foreach ($rows as $row) {
            fputcsv($file, $row);
        }

        fclose($file);

        return $filePath;
    }

    /**
     * Powiadomienie o zakończonym eksporcie.
     *
     * @param Export $export
     * @return string
     */
    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Eksport wynagrodzeń został zakończony. Wyeksportowano ' . number_format($export->successful_rows) . ' ' . str('wiersz')->plural($export->successful_rows) . '.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' Nie udało się wyeksportować ' . number_format($failedRowsCount) . ' ' . str('wiersz')->plural($failedRowsCount) . '.';
        }

        return $body;
    }
}
