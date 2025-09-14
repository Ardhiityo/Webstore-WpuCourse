<?php

declare(strict_types=1);

namespace App\Services;

use App\Contract\PaymentDriverInterface;
use App\Data\PaymentData;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\DataCollection;
use App\Drivers\Payment\OfflinePaymentDriver;

class PaymentMethodQueryService
{
    protected array $drivers = [];

    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        $this->drivers = [
            new OfflinePaymentDriver()
        ];
    }

    public function getDriver(PaymentData $paymentData): PaymentDriverInterface
    {
        return collect($this->drivers)
            ->first(fn(PaymentDriverInterface $driver) => $driver->driver === $paymentData->driver);
    }

    /**
     * Summary of getPaymentMethods
     * @return DataCollection<PaymentData>
     */
    public function getPaymentMethods(): DataCollection
    {
        return collect($this->drivers)
            ->flatMap(function ($drivers) {
                return $drivers->getMethods()->toCollection();
            })
            ->pipe(function ($items) {
                return PaymentData::collect($items, DataCollection::class);
            });
    }

    public function getPaymentMethodByHash(string $hash): ?PaymentData
    {
        return $this->getPaymentMethods()
            ->toCollection()
            ->first(fn(PaymentData $payment_data) => $payment_data->hash === $hash);
    }

    public function shouldShowButton($sales_order)
    {
        return $this->getDriver(
            $sales_order->payment_driver
        )->shouldPayButton($sales_order);
    }

    public function getRedirectUrl($sales_order): ?string
    {
        return $this->getDriver(
            $sales_order->payment->driver
        )->getRedirectUrl($sales_order);
    }
}
