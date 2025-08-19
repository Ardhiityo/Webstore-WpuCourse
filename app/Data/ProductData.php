<?php

namespace App\Data;

use App\Models\Product;
use Spatie\LaravelData\Data;
use Illuminate\Support\Number;
use Spatie\LaravelData\Attributes\Computed;

class ProductData extends Data
{
    #[Computed]
    public $price_formatted;

    public function __construct(
        public string $name,
        public string $tag,
        public string $sku,
        public string $slug,
        public string|null $description,
        public float $price,
        public int $stock,
        public int $weight,
        public $cover
    ) {
        $this->price_formatted = Number::currency($price);
    }

    public static function fromModel(Product $product): self
    {
        return new self(
            name: $product->name,
            tag: $product->tags()->where('type', 'collection')->pluck('name')->implode(', '),
            sku: $product->sku,
            slug: $product->slug,
            description: $product->description,
            price: $product->price,
            stock: $product->stock,
            weight: $product->weight,
            cover: $product->getFirstMediaUrl('cover')
        );
    }
}
