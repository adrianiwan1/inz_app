<?php

namespace App\Livewire\Action;

use Livewire\Component;
use App\Models\Action;
use App\Models\ActionHistory;
use Carbon\Carbon;

class ActionTimer extends Component
{
    public $actions = [];
    public $newAction = '';
    public $currentAction = null;
    public $startTime = null;
    public $endTime = null;
    public $elapsedTime = 0;

    public $todayActionHistories = [];

    // Obsługuje dodawanie akcji
    public function addAction()
    {
        if (empty($this->newAction)) return;

        $action = Action::create([
            'name' => $this->newAction,
            'user_id' => auth()->id(),
            'start_time' => null,
            'end_time' => null,
            'elapsed_time' => 0,
        ]);

        $this->actions[] = $action;
        $this->newAction = ''; // Resetowanie pola
    }

    // Rozpoczyna akcję
    public function startAction($actionId)
    {
        $action = Action::find($actionId);
        $this->currentAction = $actionId;
        $this->startTime = now();
        $this->elapsedTime = 0;

        // Zapisz rozpoczęcie akcji w history
        ActionHistory::create([
            'action_id' => $actionId,
            'user_id' => auth()->id(),
            'start_time' => $this->startTime,
            'elapsed_time' => 0, // Rozpoczęcie, więc czas 0
        ]);

        // Emitowanie zdarzenia, aby uruchomić licznik w frontendzie
        $this->dispatch('startTimer', ['startTime' => $this->startTime]);

        $this->dispatch('actionStarted', ['actionId' => $actionId]);
    }

    // Zatrzymuje akcję
    public function stopAction($actionId)
    {
        $this->endTime = now(); // Pobranie aktualnego czasu zakończenia

        // Znajdź najnowszy rekord historii akcji dla tego użytkownika i akcji, który nie został zakończony
        $history = ActionHistory::where('action_id', $actionId)
            ->where('user_id', auth()->id()) // Tylko dla zalogowanego użytkownika
            ->whereNull('end_time') // Znajdź otwartą akcję
            ->latest('start_time') // Najnowszy rekord na górze
            ->first();


        if ($history) {
            // Zabezpieczenie: Sprawdź, czy `start_time` i `endTime` są dostępne
            if ($history->start_time && $this->endTime) {
                $this->elapsedTime = $history->start_time->diffInSeconds($this->endTime);
            } else {
                $this->elapsedTime = 0; // Ustaw wartość domyślną w razie braku danych
            }
            dump('Validation:', $history);

            // Zaktualizuj tylko aktualny rekord
            $history->end_time = $this->endTime;
            $history->elapsed_time = $this->elapsedTime;
            $history->save();
        }

        // Zresetowanie stanu po zakończeniu
        $this->currentAction = null;

        // Emitowanie zdarzeń na frontendzie
        $this->dispatch('stopTimer', []);
        $this->dispatch('actionStopped', ['actionId' => $actionId]);
    }

    // Pobieranie danych akcji z bazy
    public function mount()
    {
        $this->actions = Action::where('user_id', auth()->id())->get();
    }

    public function render()
    {
        return view('livewire.action.action-timer');
    }

    public function loadTodayActions()
    {
        $this->todayActionHistories = ActionHistory::with('action') // Ładowanie relacji z akcją
        ->where('user_id', auth()->id()) // Akcje wykonane przez zalogowanego użytkownika
        ->whereDate('start_time', Carbon::today()) // Tylko akcje wykonane dzisiaj
        ->whereNotNull('end_time') // Tylko zakończone akcje
        ->get();
    }
}
