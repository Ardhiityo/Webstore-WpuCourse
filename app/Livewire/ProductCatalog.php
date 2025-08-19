<?php

namespace App\Livewire;

use App\Data\ProductData;
use App\Models\Product;
use Livewire\Component;

class ProductCatalog extends Component
{
    public function render()
    {
        $products = Product::paginate(9);

        ProductData::collect($products);

        return view('livewire.product-catalog', compact('products'));
    }
}
