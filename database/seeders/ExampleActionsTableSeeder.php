<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExampleActionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userId = 21; // ID użytkownika przypisanego do akcji

        DB::table('actions')->insert([
            [
                'name' => 'Testowanie systemu',
                'start_time' => null,
                'end_time' => null,
                'elapsed_time' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => $userId,
            ],
            [
                'name' => 'Uzupełnianie raportu',
                'start_time' => null,
                'end_time' => null,
                'elapsed_time' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => $userId,
            ],
            [
                'name' => 'Spotkanie zespołowe',
                'start_time' => null,
                'end_time' => null,
                'elapsed_time' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => $userId,
            ],
            [
                'name' => 'Planowanie sprintu',
                'start_time' => null,
                'end_time' => null,
                'elapsed_time' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => $userId,
            ],
            [
                'name' => 'Weryfikacja kodu',
                'start_time' => null,
                'end_time' => null,
                'elapsed_time' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => $userId,
            ],
            [
                'name' => 'Aktualizacja dokumentacji',
                'start_time' => null,
                'end_time' => null,
                'elapsed_time' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => $userId,
            ],
            [
                'name' => 'Testowanie aplikacji mobilnej',
                'start_time' => null,
                'end_time' => null,
                'elapsed_time' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => $userId,
            ],
            [
                'name' => 'Tworzenie diagramów',
                'start_time' => null,
                'end_time' => null,
                'elapsed_time' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => $userId,
            ],
            [
                'name' => 'Analiza błędów',
                'start_time' => null,
                'end_time' => null,
                'elapsed_time' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => $userId,
            ],
            [
                'name' => 'Przygotowanie prezentacji',
                'start_time' => null,
                'end_time' => null,
                'elapsed_time' => 0,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'user_id' => $userId,
            ],
        ]);
    }
}
