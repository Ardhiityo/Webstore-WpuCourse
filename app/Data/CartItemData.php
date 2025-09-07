<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Product;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class CartItemData extends Data
{
    public function __construct(
        public string $sku,
        public int $weight,
        public float $price,
        public int $quantity
    ) {}

    #[Computed]
    public function product()
    {
        return ProductData::fromModel(Product::where('sku', $this->sku)->first());
    }
}
