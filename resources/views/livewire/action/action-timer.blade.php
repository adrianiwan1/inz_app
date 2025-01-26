<div class="flex max-h-screen">
    <!-- Sidebar (Card with list) -->
    <div class="w-1/4 p-4">
        <div class="bg-white rounded-lg shadow-md p-4 max-h-full flex flex-col">
            <h5 class="text-lg font-bold mb-4">Lista akcji</h5>
            <div>
                <input wire:model="newAction" type="text" placeholder="Nowa akcja" class="border rounded p-2 w-full mb-4" />
                <button wire:click="addAction" class="text-black bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">
                    Dodaj akcję
                </button>
            </div>
            <!-- Lista akcji z ograniczeniem maksymalnej wysokości i przewijaniem -->
            <ul class="divide-y divide-gray-200 overflow-y-auto max-h-128">
                @foreach($actions as $action)
                    <li wire:key="action-{{ $action->id }}" class="py-4 flex items-center justify-between {{ $action->id == $currentAction ? 'bg-green-200' : '' }}">
                        <span class="text-gray-700">{{ $action->name }}</span>
                        <div class="flex space-x-2">
                            @if($action->id == $currentAction)
                                <!-- Jeśli akcja jest w trakcie, wyświetlamy przycisk Stop -->
                                <span class="text-sm text-gray-500">
                                    Czas:
                                    <!-- Dynamiczny czas -->
                                    <span id="timer"></span>
                                </span>
                                <button wire:click="stopAction({{ $action->id }})" class="text-black bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-5 py-2.5">
                                    Stop
                                </button>
                            @else
                                <!-- Jeśli akcja nie jest w trakcie, wyświetlamy przycisk Start -->
                                <button wire:click="startAction({{ $action->id }})" class="text-black bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-5 py-2.5">
                                    Start
                                </button>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    @include('livewire.action.partials.main-dashboard')
</div>
