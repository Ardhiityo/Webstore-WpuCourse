<?php

declare(strict_types=1);

namespace App\Services;

use App\Data\CartItemData;
use App\Contract\CartServiceInterface;
use App\Data\CartData;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Spatie\LaravelData\DataCollection;

class SessionCartService implements CartServiceInterface
{
    protected string $session_key = 'session_cart';

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function load()
    {
        $raw = session()->get($this->session_key, []);

        return new DataCollection(CartItemData::class, $raw);
    }

    public function save(Collection $item)
    {
        return session()->put($this->session_key, $item->values()->all());
    }

    public function addOrUpdate(CartItemData $item): void
    {
        $updated = false;

        $cart = $this->load()->toCollection()->map(function ($i) use ($item, &$updated) {
            if ($i->sku === $item->sku) {
                $updated = true;
                return $item;
            }
            return $i;
        })->values()->collect();

        if (!$updated) {
            $cart->push($item);
        }

        $this->save($cart);
    }

    public function getItemBySku(string $sku): CartItemData|null
    {
        return $this->load()->toCollection()->first(fn($item) => $item->sku === $sku);
    }

    public function remove(string $sku): void
    {
        $cart = $this->load()->toCollection()->reject(
            fn($item) => $item->sku === $sku
        )->values()->collect();

        $this->save($cart);
    }

    public function all(): CartData
    {
        return new CartData($this->load());
    }

    public function clear(): void
    {
        Session::forget($this->session_key);
    }
}
