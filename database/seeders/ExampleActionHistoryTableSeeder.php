<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExampleActionHistoryTableSeeder extends Seeder
{
    public function run()
    {
        $userId = 21; // ID użytkownika
        $actions = range(1, 13); // Zakres action_id
        $startDate = Carbon::now()->startOfDay()->addHours(8); // Dzisiaj o 8:00
        $endDate = Carbon::now()->endOfMonth()->endOfDay(); // Koniec miesiąca

        $data = [];

        $currentDate = clone $startDate;

        while ($currentDate->lessThanOrEqualTo($endDate)) {
            $dailyElapsedTime = 0; // Suma czasu akcji w ciągu dnia
            $currentStartTime = $currentDate->copy();

            // Generuj akcje do maksymalnie 8 godzin dziennie
            while ($dailyElapsedTime < 28800) { // 8 godzin = 28800 sekund
                $actionId = $actions[array_rand($actions)]; // Losowy action_id

                // Generowanie czasu trwania akcji (maksymalnie tyle, ile pozostało)
                $remainingTime = 28800 - $dailyElapsedTime; // Pozostały czas w sekundach
                $elapsed_time = rand(180, min($remainingTime, 10800)); // Min. 3 min (180s), max. 3 godz.

                // Oblicz czas zakończenia akcji
                $currentEndTime = $currentStartTime->copy()->addSeconds($elapsed_time);

                // Dodaj rekord do danych
                $data[] = [
                    'action_id' => $actionId,
                    'user_id' => $userId,
                    'start_time' => $currentStartTime,
                    'end_time' => $currentEndTime,
                    'elapsed_time' => $elapsed_time,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                // Zaktualizuj czas rozpoczęcia następnej akcji z losową przerwą
                $currentStartTime = $currentEndTime->copy()->addMinutes(rand(1, 15)); // Przerwa 1-15 minut

                // Zaktualizuj dzienny czas
                $dailyElapsedTime += $elapsed_time;

                // Jeśli czas wykracza poza godziny pracy, przerwij
                if ($currentStartTime->hour >= 16) {
                    break;
                }
            }

            // Przejdź do następnego dnia roboczego
            $currentDate->addDay()->startOfDay()->addHours(8); // 8:00 następnego dnia
        }

        // Dodaj dane do bazy
        DB::table('action_histories')->insert($data);
    }
}
