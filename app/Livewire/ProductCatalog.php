<?php

declare(strict_types=1);

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
        $collection_result = Tag::withType('collection')->withCount('products')->get();
        $collections = ProductCollectionData::collect($collection_result);

        $result = Product::paginate(6);
        $products = ProductData::collect($result);

        return view('livewire.product-catalog', compact('products', 'collections'));
    }
}
