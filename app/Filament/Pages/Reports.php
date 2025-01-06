<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;

use Illuminate\Database\Eloquent\Builder;
use App\Models\ActionHistory;
use Illuminate\Support\Facades\Auth;

class Reports extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    protected static ?string $navigationLabel = 'Raporty';
    protected static ?string $slug = 'reports';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected function getTableQuery(): Builder
    {
        return ActionHistory::query();
    }

    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Widgets\ReportsStatsOverviewWidget::class,
        ];
    }
    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('action.name')
                ->label('Nazwa akcji')
                ->sortable()
                ->searchable(),
            TextColumn::make('user.name')
                ->label('Nazwa użytkownika')
                ->sortable()
                ->searchable(),
            TextColumn::make('elapsed_time')
                ->label('Czas trwania')
                ->sortable()
                ->formatStateUsing(fn ($state) => gmdate('H:i:s', $state)),
            TextColumn::make('start_time')
                ->label('Czas rozpoczęcia')
                ->sortable()
                ->dateTime(),
            TextColumn::make('end_time')
                ->label('Czas zakończenia')
                ->sortable()
                ->dateTime(),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            Filter::make('date_range')
                ->form([
                    DatePicker::make('start_date')->label('Data początkowa'),
                    DatePicker::make('end_date')->label('Data końcowa'),
                ])
                ->query(function (Builder $query, array $data) {
                    return $query
                        ->when($data['start_date'], fn ($q) => $q->where('start_time', '>=', $data['start_date']))
                        ->when($data['end_date'], fn ($q) => $q->where('end_time', '<=', $data['end_date']));
                }),
            Filter::make('user')
                ->label('Nazwa użytkownika')
                ->form([
                    \Filament\Forms\Components\Select::make('user_id')
                        ->label('Nazwa użytkownika')
                        ->options(\App\Models\User::query()
                            ->pluck('name', 'id')
                            ->toArray()
                        )
                        ->placeholder('Wybierz użytkownika')
                        ->searchable(), // Umożliwia wyszukiwanie w rozwijanej liście
                ])
                ->query(function (Builder $query, array $data) {
                    // Jeżeli użytkownik nie został wybrany, pokazuj wszystkie rekordy
                    return $query->when(
                        isset($data['user_id']) && $data['user_id'],
                        fn ($q) => $q->where('user_id', $data['user_id'])
                    );
                })
                ->indicateUsing(function (array $data): ?string {
                    if (!isset($data['user_id']) || !$data['user_id']) {
                        return null;
                    }

                    $userName = \App\Models\User::find($data['user_id'])->name ?? 'Nieznany użytkownik';
                    return 'Użytkownik: ' . $userName;
                }),
        ];
    }

    protected function getTableDefaultSortColumn(): ?string
    {
        return 'start_time';
    }

    protected function getTableDefaultSortDirection(): ?string
    {
        return 'desc';
    }

    protected function getTableRecordsPerPageSelectOptions(): array
    {
        return [10, 25, 50];
    }

    protected static string $view = 'filament.pages.reports';

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('manager');
    }
}
