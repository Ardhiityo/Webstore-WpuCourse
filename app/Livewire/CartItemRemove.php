<?php

namespace App\Livewire;

use App\Contract\CartServiceInterface;
use App\Data\ProductData;
use Livewire\Component;

class CartItemRemove extends Component
{
    public string $sku;

    public function mount(ProductData $product)
    {
        $this->sku = $product->sku;
    }

    public function removeFromCart(CartServiceInterface $cart)
    {
        $cart->remove($this->sku);

        session()->flash('success', "Product {$this->sku} remove from cart.");

        $this->dispatch('cart-updated');

        return redirect()->route('cart');
    }

    public function render()
    {
        return view('livewire.cart-item-remove');
    }
}
