<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class CheckoutData extends Data
{
    #[Computed]
    public float $shipping_cost;

    #[Computed]
    public float $sub_total;

    #[Computed]
    public float $grand_total;

    public function __construct(
        public CustomerData $customer,
        public string $address_line,
        public RegionData $origin,
        public RegionData $destination,
        public CartData $cart,
        public PaymentData $payment,
        public ShippingData $shipping
    ) {
        $this->shipping_cost = $shipping->cost;
        $this->sub_total = $cart->total;
        $this->grand_total = $this->sub_total + $this->shipping_cost;
    }
}
