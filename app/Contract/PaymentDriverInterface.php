<?php

declare(strict_types=1);

namespace App\Contract;

use App\Data\PaymentData;
use Spatie\LaravelData\DataCollection;

interface PaymentDriverInterface
{
    /**
     * Summary of getMethods
     * @return DataCollection<PaymentData>
     */
    public function getMethods(): DataCollection;
    public function process($sales_order);
    public function shouldPayButton(): bool;
    public function getRedirectUrl(): ?string;
}
