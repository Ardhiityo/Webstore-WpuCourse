<?php

namespace App\Data;

use App\Models\Tag;
use App\Models\Product;
use Spatie\LaravelData\Data;

class ProductCollectionData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public int $product_count
    ) {}

    public static function fromModel(Tag $tag): self
    {
        return new self(
            $tag->id,
            (string)$tag->name,
            (string)$tag->slug,
            $tag->products_count
        );
    }
}
