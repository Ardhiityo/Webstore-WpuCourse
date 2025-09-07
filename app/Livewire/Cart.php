<?php

namespace App\Livewire;

use App\Contract\CartServiceInterface;
use Livewire\Component;

class Cart extends Component
{
    public string $sub_total;
    public string $total;

    public function mount(CartServiceInterface $service)
    {
        $this->total = $service->all()->total_formatted;
        $this->sub_total = $this->total;
    }

    public function getItemsProperty(CartServiceInterface $service)
    {
        return $service->all()->items->toCollection();
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
