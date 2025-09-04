<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Product;
use Spatie\LaravelData\Data;
use Illuminate\Support\Number;
use Spatie\LaravelData\Attributes\Computed;

class ProductData extends Data
{
    #[Computed]
    public string $price_formatted;

    public function __construct(
        public string $name,
        public string $short_desc,
        public string $sku,
        public string|null $description,
        public int $stock,
        public string $slug,
        public float $price,
        public int $weight,
        public string $cover_url
    ) {
        $this->price_formatted = Number::currency($price);
    }

    public static function fromModel(Product $product): self
    {
        return new self(
            $product->name,
            $product->tags()->where('type', 'collection')->pluck('name')->implode(', '),
            $product->sku,
            $product->description,
            $product->stock,
            $product->slug,
            (float)$product->price,
            $product->weight,
            $product->getFirstMediaUrl('cover')
        );
    }
}
