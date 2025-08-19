<?php

namespace App\Livewire;

use App\Data\ProductCollectionData;
use App\Models\Tag;
use App\Models\Product;
use Livewire\Component;
use App\Data\ProductData;

class ProductCatalog extends Component
{
    public function render()
    {
        $products = Product::paginate(9);

        ProductData::collect($products);

        $tags = Tag::query()->withType('collection')->withCount('products')->get();

        $collections = ProductCollectionData::collect($tags);

        return view('livewire.product-catalog', compact('products', 'collections'));
    }
}
