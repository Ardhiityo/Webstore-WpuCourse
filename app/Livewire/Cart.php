<?php

namespace App\Livewire;

use App\Contract\CartServiceInterface;
use Illuminate\Support\Collection;
use Livewire\Component;

class Cart extends Component
{
    public string $sub_total;
    public string $total;

    public function mount(CartServiceInterface $service)
    {
        $all = $service->all();
        $this->sub_total = $all->total_formatted;
        $this->total = $this->sub_total;
    }

    public function getItemsProperty(CartServiceInterface $service): Collection
    {
        return $service->all()->items->toCollection();
    }

    public function checkout()
    {
        return redirect()->route('checkout');
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
