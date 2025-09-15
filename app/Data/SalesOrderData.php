<?php

namespace App\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Illuminate\Support\Number;
use App\Data\SalesOrderItemData;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\DataCollectionOf;

class SalesOrderData extends Data
{
    #[Computed]
    public string $sub_total_formatted;
    #[Computed]
    public string $shipping_total_formatted;
    #[Computed]
    public string $total_formatted;
    #[Computed]
    public string $created_at_formatted;
    #[Computed]
    public string $due_date_at_formatted;

    public function __construct(
        public string $trx_id,
        public string $status,
        public CustomerData $customer,
        public string $address_line,
        public RegionData $origin,
        public RegionData $destination,
        #[DataCollectionOf(SalesOrderItemData::class)]
        public DataCollection $items,
        public SalesShippingData $sales_shipping,
        public SalesPaymentData $payment,
        public float $shipping_cost,
        public float $sub_total,
        public float $total,
        public Carbon $due_date_at,
        public Carbon $created_at
    ) {
        $this->sub_total_formatted = Number::currency($sub_total);
        $total = $shipping_cost + $sub_total;
        $this->total_formatted = Number::currency($total);
        $this->shipping_total_formatted = Number::currency($shipping_cost);
        $this->created_at_formatted = $created_at->translatedFormat('d F Y, H:i');
        $this->due_date_at_formatted = $due_date_at->translatedFormat('d F Y, H:i');
    }
}
