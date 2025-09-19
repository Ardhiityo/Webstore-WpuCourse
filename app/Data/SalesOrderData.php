<?php

declare(strict_types=1);

namespace App\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Illuminate\Support\Number;
use App\Data\SalesOrderItemData;
use App\Models\SalesOrder;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\ModelStates\State;

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
        public State $status,
        public CustomerData $customer,
        public string $address_line,
        public RegionData $origin,
        public RegionData $destination,
        #[DataCollectionOf(SalesOrderItemData::class)]
        public DataCollection $items,
        public SalesShippingData $shipping,
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

    public static function fromModel(SalesOrder $sales_order): self
    {
        return new self(
            $sales_order->trx_id,
            $sales_order->status,
            new CustomerData(
                $sales_order->customer_full_name,
                $sales_order->customer_email,
                $sales_order->customer_phone,
            ),
            $sales_order->address_line,
            new RegionData(
                $sales_order->origin_code,
                $sales_order->origin_province,
                $sales_order->origin_city,
                $sales_order->origin_district,
                $sales_order->origin_sub_district,
                $sales_order->origin_postal_code
            ),
            new RegionData(
                $sales_order->destination_code,
                $sales_order->destination_province,
                $sales_order->destination_city,
                $sales_order->destination_district,
                $sales_order->destination_sub_district,
                $sales_order->destination_postal_code
            ),
            SalesOrderItemData::collect($sales_order->items->toArray(), DataCollection::class),
            new SalesShippingData(
                $sales_order->shipping_driver,
                $sales_order->shipping_receipt_number,
                $sales_order->shipping_courier,
                $sales_order->shipping_service,
                $sales_order->shipping_estimated_delivery,
                $sales_order->shipping_cost,
                $sales_order->shipping_weight
            ),
            new SalesPaymentData(
                $sales_order->payment_driver,
                $sales_order->payment_method,
                $sales_order->payment_label,
                $sales_order->payment_payload,
                $sales_order->paid_at,
            ),
            $sales_order->shipping_total,
            $sales_order->sub_total,
            $sales_order->total,
            Carbon::parse($sales_order->due_date_at),
            $sales_order->created_at
        );
    }
}
