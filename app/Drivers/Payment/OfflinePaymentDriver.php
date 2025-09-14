<?php

declare(strict_types=1);

namespace App\Drivers\Payment;

use Spatie\LaravelData\DataCollection;
use App\Contract\PaymentDriverInterface;
use App\Data\PaymentData;

class OfflinePaymentDriver implements PaymentDriverInterface
{
    public readonly string $driver;

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->driver = 'offline';
    }

    /**
     * Summary of getMethods
     * @return DataCollection<PaymentData>
     */
    public function getMethods(): DataCollection
    {
        return PaymentData::collect([
            PaymentData::from([
                'driver' => $this->driver,
                'method' => 'bca-bank-transfer',
                'label' => 'Bank Transfer BCA',
                'payload' => [
                    'account_number' => '1234',
                    'account_hold_name' => 'Rezza Kurniawan'
                ]
            ]),
        ], DataCollection::class);
    }

    public function process($sales_order)
    {
        //
    }

    public function shouldPayButton($sales_order): bool
    {
        return false;
    }

    public function getRedirectUrl($sales_order): string|null
    {
        return null;
    }
}
