<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Product;
use Spatie\LaravelData\Data;
use Illuminate\Support\Number;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Optional;

class ProductData extends Data
{
    #[Computed]
    public string $price_formatted;

    public function __construct(
        public string $name,
        public string $short_desc,
        public string $sku,
        public string $slug,
        public int $stock,
        public int $weight,
        public float $price,
        public string $description,
        public string $cover_url,
        public array|Optional $gallery
    ) {
        $this->price_formatted = Number::currency($price);
    }

    public static function fromModel(Product $product, bool $with_gallery = false): self
    {
        return new self(
            $product->name,
            $product->tags()->where('type', 'collection')->pluck('name')->implode(', '),
            $product->sku,
            $product->slug,
            $product->stock,
            $product->weight,
            (float)$product->price,
            $product->description,
            $product->getFirstMediaUrl('cover'),
            $with_gallery ? $product->getMedia('gallery')->map(fn($row) => $row->getUrl())->toArray() : new Optional()
        );
    }
}
