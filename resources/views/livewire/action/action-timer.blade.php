<div class="flex h-screen">
    <!-- Sidebar (Card with list) -->
    <div class="w-1/4 p-4">
        <div class="bg-white rounded-lg shadow-md p-4">
            <h5 class="text-lg font-bold mb-4">Actions List</h5>
            <div>
                <input wire:model="newAction" type="text" placeholder="Nowa akcja" class="border rounded p-2 w-full mb-4" />
                <button wire:click="addAction" class="text-black bg-blue-500 hover:bg-blue-600 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-4 py-2">
                    Dodaj akcję
                </button>
            </div>
            <ul class="divide-y divide-gray-200">
                @foreach($actions as $action)
                    <li wire:key="action-{{ $action->id }}" class="py-4 flex items-center justify-between {{ $action->id == $currentAction ? 'bg-green-200' : '' }}">
                        <span class="text-gray-700">{{ $action->name }}</span>
                        <div class="flex space-x-2">
                            @if($action->id == $currentAction)
                                <!-- Jeśli akcja jest w trakcie, wyświetlamy przycisk Stop -->
                                <span class="text-sm text-gray-500">
                                    Czas:
                                    <!-- Display dynamic time -->
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
    <div class="w-3/4 p-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
            <p class="text-gray-600">Wybierz akcję z listy po lewej stronie, aby rozpocząć lub zatrzymać czas.</p>
        </div>
    </div>
</div>

@script
<script>
    let startTime = null;
    let elapsedTime = 0;
    let timerInterval = null;

    // Nasłuchiwanie zdarzenia 'startTimer'
    window.addEventListener('startTimer', () => {
        console.log('Rozpoczęto licznik.');
        startTime = Date.now(); // Pobierz bieżący czas w milisekundach
        elapsedTime = 0; // Resetowanie czasu

        if (timerInterval) {
            clearInterval(timerInterval); // Czyszczenie poprzedniego interwału
        }

        // Ustawiamy interwał do aktualizowania czasu
        timerInterval = setInterval(() => {
            const now = Date.now();
            elapsedTime = Math.floor((now - startTime) / 1000); // Oblicz upływ czasu w sekundach
            document.getElementById('timer').textContent = formatTime(elapsedTime);
        }, 1000);
    });

    // Nasłuchiwanie zdarzenia 'stopTimer'
    window.addEventListener('stopTimer', () => {
        console.log('Zatrzymano licznik.');
        if (timerInterval) {
            clearInterval(timerInterval); // Zatrzymaj interwał
            timerInterval = null;
        }
        document.getElementById('timer').textContent = '00:00:00'; // Zresetuj wyświetlany czas
    });

    // Funkcja do formatowania czasu na format HH:MM:SS
    function formatTime(seconds) {
        const hours = Math.floor(seconds / 3600);
        const minutes = Math.floor((seconds % 3600) / 60);
        const sec = seconds % 60;
        return `${pad(hours)}:${pad(minutes)}:${pad(sec)}`;
    }

    // Funkcja do dodania wiodących zer
    function pad(num) {
        return num < 10 ? '0' + num : num;
    }
</script>
@endscript
