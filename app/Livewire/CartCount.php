<?php

namespace App\Livewire;

use App\Contract\CartServiceInterface;
use Livewire\Attributes\On;
use Livewire\Component;

class CartCount extends Component
{
    public int $count;

    public function mount(CartServiceInterface $service)
    {
        $this->count = $service->all()->total_cart;
    }

    #[On('cart-updated')]
    public function updateCount(CartServiceInterface $service)
    {
        $this->count = $service->all()->total_cart;
    }

    public function render()
    {
        return view('livewire.cart-count');
    }
}
