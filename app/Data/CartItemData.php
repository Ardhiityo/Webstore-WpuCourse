<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Product;
use Livewire\Attributes\Computed;
use Spatie\LaravelData\Data;

class CartItemData extends Data
{
    public function __construct(
        public $sku,
        public $quantity,
        public $price,
        public $weight
    ) {}

    #[Computed()]
    public function product(): ProductData
    {
        return ProductData::fromModel(Product::where('sku', $this->sku)->first());
    }
}
