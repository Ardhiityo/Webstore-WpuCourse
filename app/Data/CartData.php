<?php

declare(strict_types=1);

namespace App\Data;

use App\Data\CartItemData;
use Spatie\LaravelData\Data;
use Illuminate\Support\Number;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class CartData extends Data
{
    public float $total;
    public int $total_weight;
    public int $total_quantity;
    public string $total_formatted;

    public function __construct(
        #[DataCollectionOf(CartItemData::class)] public DataCollection $items
    ) {
        $items = $items->toCollection();

        $this->total = $items->sum(fn(CartItemData $cart) => $cart->price * $cart->quantity);
        $this->total_weight = $items->sum(fn(CartItemData $cart) => $cart->weight);
        $this->total_quantity = $items->sum(fn(CartItemData $cart) => $cart->quantity);
        $this->total_formatted = Number::currency($this->total);
    }
}
