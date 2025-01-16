<?php


namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use App\Models\ActionHistory;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Reports extends Page implements Tables\Contracts\HasTable
{
    use Tables\Concerns\InteractsWithTable;

    public $selectedUser = null;
    public $startDate = null;
    public $endDate = null;

    protected static ?string $navigationLabel = 'Raporty';
    protected static ?string $slug = 'reports';
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $title = 'Informacje';

    public function mount(): void
    {
        $this->startDate = now()->startOfMonth()->toDateString();
        $this->endDate = now()->endOfMonth()->toDateString();
        $this->selectedUser = null;
    }

    protected function getFormSchema(): array
    {
        return [
                    Select::make('selectedUser')
                        ->label('Wybierz użytkownika')
                        ->options(User::query()
                            ->get()
                            ->mapWithKeys(fn ($user) => [$user->id => $user->name])
                            ->toArray()
                        )
                        ->placeholder('Wszyscy użytkownicy')
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->dispatch('filtersUpdated', $this->selectedUser, $this->startDate, $this->endDate)),

                    DatePicker::make('startDate')
                        ->label('Data początkowa')
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->dispatch('filtersUpdated', $this->selectedUser, $this->startDate, $this->endDate)),

                    DatePicker::make('endDate')
                        ->label('Data końcowa')
                        ->reactive()
                        ->afterStateUpdated(fn () => $this->dispatch('filtersUpdated', $this->selectedUser, $this->startDate, $this->endDate)),
        ];
    }

    protected function getTableQuery(): Builder
    {
        return ActionHistory::query()
            ->when($this->selectedUser, fn ($query) => $query->where('user_id', $this->selectedUser))
            ->when($this->startDate, fn ($query) => $query->where('start_time', '>=', Carbon::parse($this->startDate)))
            ->when($this->endDate, fn ($query) => $query->where('end_time', '<=', Carbon::parse($this->endDate)->setTime(23,59,59)));
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('action.name')
                ->label('Nazwa akcji')
                ->sortable()
                ->searchable(),
            TextColumn::make('user.name')
                ->label('Użytkownik')
                ->sortable()
                ->searchable(),
            TextColumn::make('user.first_name')
                ->label('Imię')
                ->sortable()
                ->searchable(),
            TextColumn::make('user.last_name')
                ->label('Nazwisko')
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

    protected function getTablePollingInterval(): ?string
    {
        return '1s';
    }

    protected function getFooterWidgets(): array
    {
        return [
            \App\Filament\Resources\ReportsResource\Widgets\ActionsPieChartWidget::class,
            \App\Filament\Resources\ReportsResource\Widgets\ActionsBarChartWidget::class,
        ];
    }

    protected static string $view = 'filament.pages.reports';

    public static function canAccess(): bool
    {
        $user = Auth::user();
        return $user && $user->hasRole('manager');
    }
}
