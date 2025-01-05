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
    public $userId;

    public $pieChartData = [];
    public $barChartData = [];

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

    public function setDefaultDateRange()
    {
        switch ($this->selectedRange) {
            case 'day':
                $this->startDate = Carbon::today();
                $this->endDate = Carbon::tomorrow();
                break;
            case 'month':
                $this->startDate = Carbon::now()->startOfMonth();
                $this->endDate = Carbon::now()->endOfMonth();
                break;
            case 'quarter':
                $this->startDate = Carbon::now()->startOfQuarter();
                $this->endDate = Carbon::now()->endOfQuarter();
                break;
            case 'year':
                $this->startDate = Carbon::now()->startOfYear();
                $this->endDate = Carbon::now()->endOfYear();
                break;
            case 'custom':
                // Domyślnie pusty zakres dla ustawień customowych
                break;
        }
    }

    public function loadCharts()
    {
        // Sprawdź, czy zakres dat został ustawiony
        if ($this->selectedRange === 'custom' && (!$this->startDate || !$this->endDate)) {
            $this->pieChartData = [];
            $this->barChartData = [];
            return;
        }

        $actions = ActionHistory::with('action')
            ->where('user_id', $this->userId)
            ->whereBetween('start_time', [$this->startDate, $this->endDate])
            ->get();

        // Dane do wykresu kołowego: ilość akcji
        $this->pieChartData = $actions->groupBy('action.name')->map(function ($group) {
            return $group->count(); // Liczba akcji
        })->toArray();

        // Dane do wykresu słupkowego: czas trwania
        $this->barChartData = $actions->groupBy('action.name')->map(function ($group) {
            $totalTime = $group->sum('elapsed_time'); // Suma czasu w sekundach
            return [
                'action' => $group->first()->action->name,
                'total_time' => $totalTime,
                'formatted_time' => gmdate('H:i:s', $totalTime), // Format godzin:minut:sekund
            ];
        })->values()->toArray();
    }


    public function render()
    {
        return view('livewire.user-activity-charts')
        ->layout('layouts.app');
    }
}
