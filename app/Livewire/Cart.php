<?php

namespace App\Livewire;

use App\Actions\ValidateCartStock;
use App\Contract\CartServiceInterface;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;
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
        try {
            ValidateCartStock::run();
            return redirect()->route('checkout');
        } catch (ValidationException $exception) {
            session()->flash('error', $exception->getMessage());
            return redirect()->route('cart');
        }
    }

    public function render()
    {
        return view('livewire.cart');
    }
}
