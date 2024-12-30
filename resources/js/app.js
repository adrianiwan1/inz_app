let counter = 0;  // Licznik
let intervalId = null;  // ID interwału

// Nasłuchuje zdarzenia 'startTimer'
document.addEventListener('startTimer', () => {
    if (intervalId === null) {  // Sprawdzamy, czy licznik już nie działa
        console.log('Rozpoczynam liczenie');
        intervalId = setInterval(() => {
            console.log(counter);
            counter++;
        }, 1000);  // Inkrementuj co 1 sekundę
    }
});

// Nasłuchuje zdarzenia 'stopTimer'
document.addEventListener('stopTimer', () => {
    if (intervalId !== null) {  // Sprawdzamy, czy licznik działa
        clearInterval(intervalId);  // Zatrzymaj interwał
        intervalId = null;  // Resetuj ID interwału
        console.log('Zatrzymano licznik');
    }
});
