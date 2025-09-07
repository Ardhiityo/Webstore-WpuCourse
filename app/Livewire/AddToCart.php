<?php

namespace App\Livewire;

use App\Contract\CartServiceInterface;
use App\Data\CartItemData;
use App\Data\ProductData;
use App\Services\SessionCartService;
use Livewire\Component;

class AddToCart extends Component
{
    public int $quantity;
    public string $sku;
    public int $weight;
    public int $stock;

    public function mount(ProductData $product, CartServiceInterface $service)
    {
        $this->sku = $product->sku;
        $this->weight = $product->weight;
        $this->stock = $product->stock;
        $this->quantity = $service->getItemBySku($this->sku)->quantity ?? 1;

        $this->validate();
    }

    public function rules(): array
    {
        return [
            'quantity' => ['required', 'int', 'min:1', "max:{$this->stock}"]
        ];
    }

    public function addToCart(SessionCartService $service)
    {
        $this->validate();

        $service->addOrUpdate(
            new CartItemData(
                $this->sku,
                $this->quantity,
                $this->weight
            )
        );

        $this->dispatch('cart-updated');

        session()->flash('success', 'Success added product to cart');

        return redirect()->route('cart');
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
