<?php

declare(strict_types=1);

namespace App\Drivers\Shipping;

use App\Data\CartData;
use App\Data\RegionData;
use App\Data\ShippingData;
use App\Data\ShippingServiceData;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Spatie\LaravelData\DataCollection;
use App\Contract\ShippingDriverInterface;

class APIKurirShippingDriver implements ShippingDriverInterface
{
    public readonly string $driver;

    public function __construct()
    {
        $this->driver = 'apikurir';
    }

    /**
     * Summary of getServices
     * @return DataCollection<ShippingServiceData>
     */
    public function getServices(): DataCollection
    {
        return ShippingServiceData::collect([
            [
                'driver' => $this->driver,
                'code' => 'jne-regular',
                'courier' => 'JNE',
                'service' => 'Regular'
            ],
            [
                'driver' => $this->driver,
                'code' => 'jne-sameday',
                'courier' => 'JNE',
                'service' => 'Same Day'
            ],
            [
                'driver' => $this->driver,
                'code' => 'ninja-xpress-regular',
                'courier' => 'Ninja Xpress',
                'service' => 'Regular'
            ]
        ], DataCollection::class);
    }

    public function getRate(
        RegionData $origin,
        RegionData $destination,
        CartData $cart,
        ShippingServiceData $shipping_service
    ): ?ShippingData {

        $response = Http::withBasicAuth(
            username: config('shipping.api_kurir.username'),
            password: config('shipping.api_kurir.password')
        )->post('https://sandbox.apikurir.id/shipments/v1/open-api/rates', [
            'isUseInsurance' => true,
            'isPickup' => true,
            'isCod' => false,
            'weight' => $cart->total_weight,
            'packagePrice' => $cart->total,
            'dimensions' => [10, 10, 10],
            'origin' => [
                'postalCode' => $origin->postal_code
            ],
            'destination' => [
                'postalCode' => $destination->postal_code
            ],
            'logistics' => [$shipping_service->courier],
            'services' => [$shipping_service->service],
        ])->throw();

        $data = $response->collect('data')->flatten(1)->values()->first();

        if (empty($data)) {
            return null;
        }

        $est = data_get($data, 'minDuration') . '-' . data_get($data, 'maxDuration') . ' ' . data_get($data, 'durationType');

        return new ShippingData(
            $this->driver,
            $shipping_service->courier,
            $shipping_service->service,
            $est,
            data_get($data, 'price'),
            data_get($data, 'weight'),
            $origin,
            $destination,
            data_get($data, 'logoUrl')
        );
    }
}
