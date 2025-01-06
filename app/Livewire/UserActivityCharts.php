<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\ActionHistory;
use Carbon\Carbon;

class UserActivityCharts extends Component
{
    public $startDate;
    public $endDate;
    public $selectedRange = 'month'; // Domyślny zakres czasu
    public $customDateRange;
    public $userId;

    public $pieChartData = [];
    public $barChartData = [];

    public $activityDetails = [];

    public $currentPage = 1;


    public function previousPage()
    {
        $this->currentPage = max(1, $this->currentPage - 1);
    }

    public function nextPage()
    {
        $this->currentPage++;
    }
    public function mount()
    {
        $this->userId = auth()->id(); // ID zalogowanego użytkownika

        // Ustaw domyślny zakres na aktualny dzień
        $this->startDate = Carbon::today(); // Początek dnia
        $this->endDate = Carbon::tomorrow()->subSecond(); // Koniec dnia (23:59:59)

        $this->selectedRange = 'day'; // Domyślnie ustaw "dzień"

        // Wczytaj dane wykresów
        $this->loadCharts();
    }

    public function updatedSelectedRange()
    {
        $this->setDefaultDateRange();
        $this->loadCharts();
    }

    public function setDateRange($range)
    {
        $this->selectedRange = $range;
        $this->setDefaultDateRange();
        $this->loadCharts();
    }

    public function setDefaultDateRange()
    {
        switch ($this->selectedRange) {
            case 'day':
                $this->startDate = Carbon::today();
                $this->endDate = Carbon::today()->endOfDay();
                break;
            case 'week':
                $this->startDate = Carbon::now()->startOfWeek();
                $this->endDate = Carbon::now()->endOfWeek();
                break;
            case 'month':
                $this->startDate = Carbon::now()->startOfMonth();
                $this->endDate = Carbon::now()->endOfMonth();
                break;
            case 'year':
                $this->startDate = Carbon::now()->startOfYear();
                $this->endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                // Zakres zostanie ustawiony w `applyCustomDateRange`
                break;
        }
    }

    public function applyCustomDateRange()
    {
        if ($this->customDateRange) {
            [$this->startDate, $this->endDate] = explode(' to ', $this->customDateRange);
            $this->startDate = Carbon::parse($this->startDate)->startOfDay();
            $this->endDate = Carbon::parse($this->endDate)->endOfDay();
            $this->loadCharts(); // Wywołaj załadowanie danych i dispatch
        }
    }

    public function loadCharts()
    {
        if ($this->selectedRange === 'custom' && (!$this->startDate || !$this->endDate)) {
            $this->pieChartData = [];
            $this->barChartData = [];
            $this->activityDetails = [];
            logger('Custom range: brak daty startowej lub końcowej');
            return;
        }

        // Pobranie akcji z bazy danych
        $actions = ActionHistory::with('action')
            ->where('user_id', $this->userId)
            ->whereBetween('start_time', [$this->startDate, $this->endDate])
            ->get();

        logger('Akcje pobrane z bazy danych:', $actions->toArray());

        // Dane do wykresu kołowego: liczba akcji
        $this->pieChartData = $actions->groupBy('action.name')->map(function ($group) {
            return $group->count();
        })->toArray();
        logger('Dane do wykresu kołowego:', $this->pieChartData);

        // Dane do wykresu słupkowego: czas trwania
        $this->barChartData = $actions->groupBy('action.name')->map(function ($group) {
            $totalTime = $group->sum('elapsed_time');
            return [
                'action' => $group->first()->action->name,
                'total_time' => $totalTime,
            ];
        })->values()->toArray();
        logger('Dane do wykresu słupkowego:', $this->barChartData);

        // Dane szczegółowe dla tabeli
        $this->activityDetails = $actions->map(function ($action) {
            return [
                'action' => $action->action->name,
                'start_time' => $action->start_time,
                'end_time' => $action->end_time,
                'elapsed_time' => $action->elapsed_time,
            ];
        });

        // Wysyłanie zdarzenia do frontendu
        $this->dispatch('chartsUpdated', [
            'pieChartData' => $this->pieChartData,
            'barChartData' => $this->barChartData,
        ]);
    }



    public function render()
    {
        return view('livewire.user-activity-charts')
            ->layout('layouts.app');
    }
}
