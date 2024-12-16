<!-- resources/views/livewire/action/action-timer.blade.php -->
<div>
    <div>
        <input wire:model="newAction" type="text" placeholder="Nowa akcja" />
        <button wire:click="addAction">Dodaj akcję</button>
    </div>

    <ul>
        @foreach($actions as $action)
            <li wire:key="action-{{ $action->id }}" class="{{ $action->id == $currentAction ? 'bg-green-200' : '' }}">
                <div>
                    <span>{{ $action->name }}</span>
                    @if($action->start_time && !$action->end_time)
                        <span> Czas: {{ \Carbon\Carbon::parse($action->start_time)->diffInSeconds(now()) }} sek</span>
                        <button wire:click="stopAction({{ $action->id }})">Stop</button>
                    @else
                        <button wire:click="startAction({{ $action->id }})">Start</button>
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
</div>
