<?php

namespace App\Data;

use Spatie\LaravelData\Data;
use Illuminate\Support\Number;
use Livewire\Attributes\Computed;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class CartData extends Data
{
    #[Computed()]
    public $total;
    public $total_weight;
    public $total_quantity;
    public $total_formatted;

    public function __construct(
        #[DataCollectionOf(CartItemData::class)]
        public DataCollection $items
    ) {
        $items = $items->toCollection();

        $this->total = $items->sum(fn(CartItemData $item) => $item->price * $item->quantity);

        $this->total_weight = $items->sum(fn(CartItemData $item) => $item->weight ?? 0);

        $this->total_quantity = $items->sum(fn(CartItemData $item) => $item->quantity);

        $this->total_formatted = Number::currency($this->total);
    }
}
