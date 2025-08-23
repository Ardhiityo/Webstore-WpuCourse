<?php

namespace App\Livewire;

use App\Data\CartItemData;
use Livewire\Component;
use App\Data\ProductData;
use App\Contract\CartServiceInterface;

class AddToCart extends Component
{
    public int $quantity;
    public string $sku;
    public int $weight;
    public int $stock;
    public float $price;
    public string $label = 'Add To Cart';

    public function addToCart(CartServiceInterface $cart)
    {
        $this->validate();

        $cart->addOrUpdate(new CartItemData(
            sku: $this->sku,
            quantity: $this->quantity,
            weight: $this->weight,
            price: $this->price
        ));

        $this->dispatch('cart-updated');

        return redirect()->route('cart');
    }

    public function mount(ProductData $product, CartServiceInterface $cart)
    {
        $this->sku = $product->sku;
        $this->price = $product->price;
        $this->stock = $product->stock;
        $this->weight = $product->weight;
        $this->quantity = $cart->getItemBySku($product->sku)->quantity ?? 1;

        $this->validate();
    }

    public function rules()
    {
        return [
            'quantity' => 'required|integer|min:1|max:' . $this->stock
        ];
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
