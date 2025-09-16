<?php

declare(strict_types=1);

namespace App\Drivers\Payment;

use Spatie\LaravelData\DataCollection;
use App\Contract\PaymentDriverInterface;
use App\Data\PaymentData;
use App\Data\SalesOrderData;
use App\Models\SalesOrder;

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

    public function process(SalesOrderData $sales_order)
    {
        SalesOrder::where('trx_id', $sales_order->trx_id)->update([
            'payment_payload' => [
                'key' => 'value'
            ]
        ]);
    }

    public function shouldShowPayButton(SalesOrderData $sales_order): bool
    {
        return false;
    }

    public function getRedirectUrl(SalesOrderData $sales_order): string|null
    {
        return null;
    }
}
