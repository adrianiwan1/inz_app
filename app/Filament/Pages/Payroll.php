<?php

namespace App\Filament\Pages;

use App\Filament\Exports\PayrollExporter;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\ExportAction;
use Illuminate\Database\Eloquent\Builder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Payroll extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public $selectedMonth;
    public $selectedYear;

    protected static ?string $navigationLabel = 'Wynagrodzenia';
    protected static ?string $slug = 'payroll';
    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';

    protected static string $view = 'filament.pages.payroll';

    public function mount()
    {
        $this->selectedMonth = now()->format('m');
        $this->selectedYear = now()->format('Y');
    }

    protected function getTableQuery(): Builder
    {
        $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
        $endOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

        return User::query()->with(['actionHistories' => function ($query) use ($startOfMonth, $endOfMonth) {
            $query->whereBetween('start_time', [$startOfMonth, $endOfMonth]);
        }]);
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('name')
                ->label('Imię i nazwisko')
                ->sortable()
                ->searchable(),

            TextColumn::make('email')
                ->label('Email')
                ->sortable()
                ->searchable(),

            TextColumn::make('total_hours')
                ->label('Przepracowane godziny')
                ->getStateUsing(function (User $record) {
                    $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
                    $endOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

                    $totalSeconds = $record->actionHistories()
                        ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
                        ->sum('elapsed_time');

                    $hours = intdiv($totalSeconds, 3600);
                    $minutes = intdiv($totalSeconds % 3600, 60);
                    $seconds = $totalSeconds % 60;

                    return sprintf('%02dh %02dm %02ds', $hours, $minutes, $seconds);
                }),

            TextColumn::make('total_salary')
                ->label('Wynagrodzenie (zł)')
                ->getStateUsing(function (User $record) {
                    if ($record->employment_type === 'employment') {
                        return 'Nie dotyczy';
                    }

                    $startOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->startOfMonth();
                    $endOfMonth = Carbon::create($this->selectedYear, $this->selectedMonth, 1)->endOfMonth();

                    $totalSeconds = $record->actionHistories()
                        ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
                        ->sum('elapsed_time');

                    $hourlyRate = $record->hourly_rate ?? 0;
                    $totalHours = $totalSeconds / 3600;

                    return number_format(($hourlyRate / 100) * $totalHours, 2, ',', ' ') . ' zł';
                }),
        ];
    }

    public function updated($property)
    {
        if (in_array($property, ['selectedMonth', 'selectedYear'])) {
            $this->dispatch('$refresh');
        }
    }

    protected function getTableHeaderActions(): array
    {
        return [
            ExportAction::make('export')
            ->label('Eksportuj do CSV')
            ->exporter(PayrollExporter::class) // Użyj niestandardowego eksportera
            ->fileName(function () {
                $year = $this->selectedYear;
                $month = str_pad($this->selectedMonth, 2, '0', STR_PAD_LEFT);
                return "payroll_{$year}_{$month}";
            })
            ->formats([ExportFormat::Csv, ExportFormat::Xlsx]),
        ];
    }

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('manager');
    }

}
