import flatpickr from "flatpickr";
import "flatpickr/dist/flatpickr.min.css";

let startTime = null;
let elapsedTime = 0;
let timerInterval = null;

// Nasłuchiwanie zdarzenia 'startTimer'
window.addEventListener('startTimer', () => {
    // console.log('Rozpoczęto licznik.');
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
    // console.log('Zatrzymano licznik.');
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
