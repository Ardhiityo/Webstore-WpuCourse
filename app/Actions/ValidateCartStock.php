<?php

namespace App\Actions;

use App\Contract\CartServiceInterface;
use Illuminate\Validation\ValidationException;
use Lorisleiva\Actions\Concerns\AsAction;

class ValidateCartStock
{
    use AsAction;

    public function __construct(public CartServiceInterface $cart) {}

    public function handle()
    {
        $insufficient = collect([]);

        foreach ($this->cart->all()->items as $item) {
            if ($item->product()->stock < $item->quantity) {
                $insufficient->push([
                    'sku' => $item->sku,
                    'name' => $item->product()->name,
                    'requested' => $item->quantity,
                    'available' => $item->product()->stock,
                ]);
            }
        }

        if ($insufficient->isNotEmpty()) {
            return ValidationException::withMessages([
                'cart' => [
                    'Some items in your cart are out of stock.',
                    'details' => $insufficient
                ],
            ]);
        }
    }
}
