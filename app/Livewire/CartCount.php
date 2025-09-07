<?php

namespace App\Livewire;

use App\Services\SessionCartService;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCount extends Component
{
    public int $count = 0;

    public function mount(SessionCartService $service)
    {
        $this->count = $service->all()->total_quantity;
    }

    #[On('cart-updated')]
    public function updateCount(SessionCartService $service)
    {
        $this->count = $service->all()->total_quantity;
    }

    public function render()
    {
        return view('livewire.cart-count');
    }
}
