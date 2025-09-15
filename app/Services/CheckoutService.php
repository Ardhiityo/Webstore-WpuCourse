<?php

namespace App\Services;

use App\Data\CheckoutData;
use App\Data\SalesOrderData;

class CheckoutService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function makeAnOrder(CheckoutData $checkout_data): SalesOrderData
    {
        return new SalesOrderData();
    }
}
