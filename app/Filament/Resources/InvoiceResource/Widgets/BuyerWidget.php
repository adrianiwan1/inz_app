<?php

namespace App\Filament\Resources\InvoiceResource\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use App\Models\Buyer;

class BuyerWidget extends Widget
{
    protected static string $view = 'filament.resources.invoice-resource.widgets.buyer-widget';

    public $buyer_name;
    public $buyer_address;
    public $buyer_nip;

    public function mount()
    {
        // Pobierz istniejące dane nabywcy z bazy danych
        $buyer = Buyer::first();

        if ($buyer) {
            $this->buyer_name = $buyer->name;
            $this->buyer_address = $buyer->address;
            $this->buyer_nip = $buyer->nip;
        }
    }

    public function save()
    {
        // Sprawdź, czy użytkownik ma rolę manager
        if (!Auth::user()->hasRole('manager')) {
            abort(403, 'Nie masz uprawnień do wykonania tej akcji.');
        }

        $this->validate([
            'buyer_name' => 'required|string|max:255',
            'buyer_address' => 'required|string|max:255',
            'buyer_nip' => 'required|string|max:20',
        ]);

        // Zapisz dane nabywcy do tabeli buyers
        Buyer::updateOrCreate(
            ['id' => 1], // Stały rekord dla jedynego nabywcy
            [
                'name' => $this->buyer_name,
                'address' => $this->buyer_address,
                'nip' => $this->buyer_nip,
            ]
        );

        session()->flash('success', 'Dane nabywcy zostały zaktualizowane.');
    }

    public static function canView(): bool
    {
        // Widget dostępny tylko dla użytkowników z rolą manager
        return Auth::user()->hasRole('manager');
    }
}
