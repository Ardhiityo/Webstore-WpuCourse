<?php

namespace App\Data;

use App\Data\CartItemData;
use Spatie\LaravelData\Data;
use Illuminate\Support\Number;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class CartData extends Data
{
    public int $total_cart;
    public float $total;
    public string $total_formatted;

    public function __construct(
        #[DataCollectionOf(CartItemData::class)] public DataCollection $items
    ) {
        $items = $items->toCollection();
        $this->total_cart = $items->sum(fn($item) => $item->quantity);
        $this->total = $items->sum(fn($item) => $item->quantity * $item->product()->price);
        $this->total_formatted = Number::currency($this->total);
    }
}
