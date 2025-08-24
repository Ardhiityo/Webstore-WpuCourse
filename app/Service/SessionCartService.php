<?php

declare(strict_types=1);

namespace App\Service;

use App\Data\CartData;
use App\Data\CartItemData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use App\Contract\CartServiceInterface;
use Spatie\LaravelData\DataCollection;
use Illuminate\Support\Facades\Session;

class SessionCartService implements CartServiceInterface
{
    protected $session_key = 'cart';

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    protected function load(): DataCollection
    {
        $raw = Session::get($this->session_key, []);

        return new DataCollection(CartItemData::class, $raw);
    }

    public function save(Collection $items)
    {
        Session::put($this->session_key, $items->values()->all());
    }

    public function addOrUpdate(CartItemData $item)
    {
        $collection = $this->load()->toCollection();

        $updated = false;

        $cart = $collection->map(function (CartItemData $i) use ($item, &$updated) {
            if ($i->sku === $item->sku) {
                $i->quantity = $item->quantity;
                $updated = true;
            }
            return $i;

            // values = untuk mereset index collection
            // collect = untuk mengembalikan collection
        })->values()->collect();

        if (!$updated) {
            $cart->push($item);
        }

        $this->save($cart);
    }

    public function remove(string $sku)
    {
        $carts = $this->load()->toCollection()->reject(fn(CartItemData $item) => $item->sku === $sku)
            ->values()->collect();

        $this->save($carts);
    }

    public function getItemBySku(string $sku): ?CartItemData
    {
        return $this->load()->toCollection()->first(fn(CartItemData $item) => $item->sku === $sku);
    }

    public function all(): CartData
    {
        return new CartData($this->load());
    }
}
