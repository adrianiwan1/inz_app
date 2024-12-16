<?php

namespace App\Livewire\Action;

use Livewire\Component;
use App\Models\Action;

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
        $this->currentAction = $actionId;
        $this->startTime = now();
        $this->elapsedTime = 0;

        $action = Action::find($actionId);
        $action->start_time = $this->startTime;
        $action->save();
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

        $this->currentAction = null;
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
