<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ProfileB2BSettings extends Component
{
    public $seller_name;
    public $seller_address;
    public $seller_nip;
    public $bank_account_number;
    public $vat_rate;

    public function mount()
    {
        $user = Auth::user();
        $this->seller_name = $user->seller_name;
        $this->seller_address = $user->seller_address;
        $this->seller_nip = $user->seller_nip;
        $this->bank_account_number = $user->bank_account_number;
        $this->vat_rate = $user->vat_rate;
    }

    public function save()
    {
        $this->validate([
            'seller_name' => 'required|string|max:255',
            'seller_address' => 'required|string|max:255',
            'seller_nip' => 'required|string|max:255',
            'bank_account_number' => 'required|string|max:255',
            'vat_rate' => 'required|numeric|min:0|max:100',
        ]);

        $user = Auth::user();
        $user->update([
            'seller_name' => $this->seller_name,
            'seller_address' => $this->seller_address,
            'seller_nip' => $this->seller_nip,
            'bank_account_number' => $this->bank_account_number,
            'vat_rate' => $this->vat_rate,
        ]);

        session()->flash('success', 'Dane zostały zaktualizowane.');
    }

    public function render()
    {
        return view('livewire.profile-b2-b-settings');

    }
}
