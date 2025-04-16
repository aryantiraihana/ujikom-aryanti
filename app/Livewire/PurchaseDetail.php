<?php

namespace App\Livewire;

use App\Models\Purchase;
use Livewire\Component;

class PurchaseDetail extends Component
{
    public $purchase;

    public function mount($id){
        $this->purchase = Purchase::with(['items.product','user', 'member'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.purchase-detail');
    }
}
