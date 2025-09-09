<?php

namespace App\Actions;

use Illuminate\Support\Facades\Log;
use App\Contract\CartServiceInterface;
use Lorisleiva\Actions\Concerns\AsAction;
use Illuminate\Validation\ValidationException;

class ValidateCartStock
{
    use AsAction;

    public function __construct(private CartServiceInterface $service) {}

    public function handle()
    {
        $carts = $this->service->all()->items->toCollection();

        if ($carts->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'The products in the cart is empty.',
            ]);
        }

        $insufficient = collect([]);

        foreach ($carts as $key => $cart) {
            $product = $cart->product();
            if (!$product || $cart->quantity > $product->stock) {
                $insufficient->push([
                    'sku' => $cart->sku,
                    'name' => $product->name ?? 'Unknown',
                    'requested' => $cart->quantity,
                    'available' => $product->stock ?? 0
                ]);
            }
        }

        if ($insufficient->isNotEmpty()) {
            throw ValidationException::withMessages([
                'cart' => 'The products in the cart exceed the available stock.',
                'details' => $insufficient
            ]);
        }
    }
}
