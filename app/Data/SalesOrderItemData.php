<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Illuminate\Support\Number;

class SalesOrderItemData extends Data
{
    public string $price_formatted;
    public string $total_formatted;

    public function __construct(
        public string $name,
        public string $shot_desc,
        public string $sku,
        public string $slug,
        public string|null $description,
        public string $cover_url,
        public int $quantity,
        public float $price,
        public float $total,
        public int $weight
    ) {
        $this->price_formatted = Number::currency($price);
        $this->total_formatted = Number::currency($total);
    }
}
