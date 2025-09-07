<?php

namespace App\Livewire;

use App\Data\ProductData;
use App\Models\Product;
use Livewire\Component;

class HomePage extends Component
{
    public function render()
    {
        $feature_products = ProductData::collect(
            Product::inRandomOrder()->limit(3)->get()
        );
        $latest_products = ProductData::collect(
            Product::latest()->limit(3)->get()
        );
        return view('livewire.home-page', compact('feature_products', 'latest_products'));
    }
}
