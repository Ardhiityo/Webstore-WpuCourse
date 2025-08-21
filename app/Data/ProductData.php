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
        public string $cover,
        public Optional|array $gallery = new Optional()
    ) {
        $this->price_formatted = Number::currency($price);
    }

    public static function fromModel(Product $product, bool $with_gallery = false): self
    {
        return new self(
            name: $product->name,
            tag: $product->tags()->where('type', 'collection')->pluck('name')->implode(', '),
            sku: $product->sku,
            slug: $product->slug,
            description: $product->description,
            price: (float)$product->price,
            stock: $product->stock,
            weight: $product->weight,
            cover: $product->getFirstMediaUrl('cover'),
            gallery: $with_gallery
                ? $product->getMedia('gallery')->map(fn($media) => $media->getUrl())->toArray()
                : new Optional()
        );
    }
}
