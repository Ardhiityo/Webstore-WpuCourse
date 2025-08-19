<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Tag;
use Spatie\LaravelData\Data;

class ProductCollectionData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public string $slug,
        public int $products_count
    ) {}

    public static function fromModel(Tag $tags): self
    {
        return new self(
            id: $tags->id,
            name: (string) $tags->name,
            slug: (string) $tags->slug,
            products_count: (int) $tags->products_count
        );
    }
}
