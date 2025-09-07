<?php

namespace App\Livewire;

use Livewire\Component;
use App\Data\ProductData;
use App\Data\CartItemData;
use App\Contract\CartServiceInterface;

class AddToCart extends Component
{
    public int $quantity = 1;
    public string $sku;
    public float $price;
    public float $weight;
    public int $stock;

    protected function rules(): array
    {
        return [
            'quantity' => ['required', 'integer', 'min:1', "max:{$this->stock}"]
        ];
    }

    public function mount(ProductData $product, CartServiceInterface $service)
    {
        $this->sku = $product->sku;
        $this->price = $product->price;
        $this->weight = $product->weight;
        $this->stock = $product->stock;
        $this->quantity = $service->getItemBySku($this->sku)->quantity ?? 1;

        $this->validate();
    }

    public function addToCart(CartServiceInterface $service)
    {
        $this->validate();

        $service->addOrUpdate(new CartItemData(
            $this->sku,
            $this->weight,
            $this->price,
            $this->quantity
        ));

        $this->dispatch('cart-updated');

        session()->flash('success', 'Success added to cart!');

        return redirect()->route('cart');
    }

    public function render()
    {
        return view('livewire.add-to-cart');
    }
}
