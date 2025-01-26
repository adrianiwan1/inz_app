<?php

namespace App\Filament\Resources\ReportsResource\Widgets;

use App\Models\ActionHistory;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class ActionsPieChartWidget extends ChartWidget
{
    protected static ?string $heading = 'Liczba akcji';

    public $filters = [
        'selectedUser' => null,
        'startDate' => null,
        'endDate' => null,
    ];

    protected function getListeners(): array
    {
        return [
            'filtersUpdated' => 'updateFilters',
        ];
    }

    public function updateFilters($selectedUser, $startDate, $endDate): void
    {
        $this->filters = compact('selectedUser', 'startDate', 'endDate');
    }

    protected function getData(): array
    {
        $query = ActionHistory::query()
            ->when($this->filters['selectedUser'], fn ($query) => $query->where('user_id', $this->filters['selectedUser']))
            ->when($this->filters['startDate'], fn ($query) => $query->where('start_time', '>=', Carbon::parse($this->filters['startDate'])))
            ->when($this->filters['endDate'], fn ($query) => $query->where('end_time', '<=', Carbon::parse($this->filters['endDate'])->setTime(23, 59, 59)));

        // Pobranie danych o akcjach
        $actionCounts = $query
            ->selectRaw('action_id, COUNT(*) as count')
            ->groupBy('action_id')
            ->pluck('count', 'action_id');

        // Mapowanie etykiet akcji
        $labels = $actionCounts->keys()->map(function ($actionId) {
            return ActionHistory::query()
                ->where('action_id', $actionId)
                ->first()?->action->name ?? 'Nieznana akcja';
        })->toArray();

        $counts = $actionCounts->values()->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Liczba akcji',
                    'type' => 'pie',
                    'data' => $counts,
                    'backgroundColor' => $this->generateUniqueColors(count($labels)),
                ],
            ],
        ];
    }

    /**
     * Generowanie unikalnych kolorów dla każdej etykiety.
     */
    private function generateUniqueColors(int $count): array
    {
        $colors = [];

        for ($i = 0; $i < $count; $i++) {
            $hue = ($i * 137.5) % 360; // Użycie złotej liczby do równomiernego rozkładu kolorów
            $colors[] = $this->hslToHex($hue, 70, 50); // Jasność i nasycenie ustawione na stałe
        }

        return $colors;
    }

    /**
     * Konwersja HSL na format HEX.
     */
    private function hslToHex(float $hue, float $saturation, float $lightness): string
    {
        $c = (1 - abs(2 * $lightness / 100 - 1)) * $saturation / 100;
        $x = $c * (1 - abs(fmod($hue / 60, 2) - 1));
        $m = $lightness / 100 - $c / 2;

        if ($hue < 60) {
            [$r, $g, $b] = [$c, $x, 0];
        } elseif ($hue < 120) {
            [$r, $g, $b] = [$x, $c, 0];
        } elseif ($hue < 180) {
            [$r, $g, $b] = [0, $c, $x];
        } elseif ($hue < 240) {
            [$r, $g, $b] = [0, $x, $c];
        } elseif ($hue < 300) {
            [$r, $g, $b] = [$x, 0, $c];
        } else {
            [$r, $g, $b] = [$c, 0, $x];
        }

        $r = dechex((int)(($r + $m) * 255));
        $g = dechex((int)(($g + $m) * 255));
        $b = dechex((int)(($b + $m) * 255));

        return '#' . str_pad($r, 2, '0', STR_PAD_LEFT)
            . str_pad($g, 2, '0', STR_PAD_LEFT)
            . str_pad($b, 2, '0', STR_PAD_LEFT);
    }

    protected function getOptions(): array
    {
        return [
            'scales' => [
                'y' => [
                    'display' => false,
                ],
                'x' => [
                    'display' => false,
                ],
            ],
        ];
    }

    protected static ?string $pollingInterval = '1s';
    protected static bool $isLazy = false;

    protected function getType(): string
    {
        return 'pie';
    }
}
