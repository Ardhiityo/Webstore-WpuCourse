<?php

namespace App\Data;

use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class CartData extends Data
{
    public int $total_cart;

    public function __construct(
        #[DataCollectionOf(CartItemData::class)] public DataCollection $items
    ) {
        $items = $items->toCollection();
        $this->total_cart = $items->sum(fn($item) => $item->quantity);
    }
}
