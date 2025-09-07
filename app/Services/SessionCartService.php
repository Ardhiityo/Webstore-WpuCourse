<?php

namespace App\Services;

use App\Data\CartData;
use App\Data\CartItemData;
use Illuminate\Support\Collection;
use App\Contract\CartServiceInterface;
use Spatie\LaravelData\DataCollection;

class SessionCartService implements CartServiceInterface
{
    private string $session_key = 'cart';

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function load(): DataCollection
    {
        $raw = session()->get($this->session_key, []);

        return new DataCollection(CartItemData::class, $raw);
    }

    public function save(Collection $item): void
    {
        session()->put($this->session_key, $item->toArray());
    }

    public function addOrUpdate(CartItemData $item): void
    {
        $current_carts = $this->load()->toCollection();

        $item_existing = false;

        $new_carts = $current_carts->map(function (CartItemData $cart) use ($item, &$item_existing) {
            if ($cart->sku === $item->sku) {
                $item_existing = true;
                return $item;
            }
            return $cart;
        })->values()->collect();

        if (!$item_existing) {
            $new_carts->push($item);
        }

        $this->save($new_carts);
    }

    public function getItemBySku(string $sku): CartItemData|null
    {
        return $this->load()->toCollection()->first(fn(CartItemData $item) => $item->sku === $sku);
    }

    public function remove(string $sku): void
    {
        $new_carts = $this->load()->toCollection()->reject(fn($item) => $item->sku === $sku);

        $this->save($new_carts);
    }

    public function all(): CartData
    {
        return new CartData($this->load());
    }
}
