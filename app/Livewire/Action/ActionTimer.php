<?php

namespace App\Livewire\Action;

use Livewire\Component;
use App\Models\Action;
use App\Models\ActionHistory;

class ActionTimer extends Component
{
    public $actions = [];
    public $newAction = '';
    public $currentAction = null;
    public $startTime = null;
    public $endTime = null;
    public $elapsedTime = 0;

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
        $this->endTime = now();
        $this->elapsedTime = $this->startTime->diffInSeconds($this->endTime);

        $action = Action::find($actionId);
        $action->end_time = $this->endTime;
        $action->elapsed_time = $this->elapsedTime;
        $action->save();

        // Zapisz zakończenie akcji w history
        ActionHistory::where('action_id', $actionId)
            ->whereNull('end_time')
            ->update([
                'end_time' => $this->endTime,
                'elapsed_time' => $this->elapsedTime,
            ]);

        $this->currentAction = null;  // Resetowanie stanu po zakończeniu
        // Emitowanie zdarzenia, aby zatrzymać licznik w frontendzie
        $this->dispatch('stopTimer', []);

        $this->dispatch('actionStopped', ['actionId' => $actionId]);
    }

    // Pobieranie danych akcji z bazy
    public function mount()
    {
        $this->actions = Action::all(); // Załadowanie akcji z bazy
    }

    public function render()
    {
        return view('livewire.action.action-timer');
    }
}
