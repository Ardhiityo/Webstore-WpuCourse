<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class Checkout extends Component
{
    public function mount()
    {
        $gate = Gate::inspect('product-available');

        if ($gate->denied()) {
            return redirect()->route('cart');
        }
    }

    public function render()
    {
        return view('livewire.checkout');
    }
}
