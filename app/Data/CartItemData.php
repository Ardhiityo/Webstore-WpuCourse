<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Product;
use Spatie\LaravelData\Data;

class CartItemData extends Data
{
    public function __construct(
        public string $sku,
        public int $quantity,
        public int $weight
    ) {}

    public function product(): ProductData
    {
        return ProductData::fromModel(Product::where('sku', $this->sku)->first());
    }
}
