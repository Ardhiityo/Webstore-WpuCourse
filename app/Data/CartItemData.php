<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Product;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Attributes\Computed;

class CartItemData extends Data
{
    public function __construct(
        public $sku,
        public $quantity,
        public $price,
        public $weight
    ) {}

    #[Computed]
    public function product(): ProductData
    {
        return ProductData::fromModel(Product::where('sku', $this->sku)->first());
    }
}
