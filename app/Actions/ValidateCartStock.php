<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use App\Contract\CartServiceInterface;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Validation\ValidationException;

class ValidateCartStock
{
    use AsAction;

    public function __construct(public CartServiceInterface $cart) {}

    public function handle()
    {
        $insufficient = [];

        foreach ($this->cart->all()->items as $item) {
            $product = $item->product();
            if (!$product || $product->stock < $item->quantity) {
                $insufficient[] = [
                    'sku' => $item->sku,
                    'name' => $product->name,
                    'requested' => $item->quantity,
                    'available' => $product->stock,
                ];
            }
        }

        if ($insufficient) {
            return throw ValidationException::withMessages([
                'cart' => [
                    'Some items in your cart are out of stock.',
                    'detail' => $insufficient,
                ],
            ]);
        }
    }
}
