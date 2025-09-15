<?php

namespace App\Data;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Illuminate\Support\Number;
use Spatie\LaravelData\Attributes\Computed;

class SalesPaymentData extends Data
{
    #[Computed]
    public string $sub_total_formatted;
    #[Computed]
    public string $shipping_total_formatted;
    #[Computed]
    public string $total_formatted;
    #[Computed]
    public string $created_at_formatted;

    public function __construct(
        public string $driver,
        public string $method,
        public string $label,
        public array $payload,
        public Carbon|null $paid_at,
        public float $sub_total,
        public float $shipping_cost,
        public float $total,
        public Carbon $due_date_at,
        public Carbon $created_at
    ) {
        $this->sub_total_formatted = Number::currency($sub_total);
        $this->shipping_total_formatted = Number::currency($shipping_cost);
        $this->total_formatted = Number::currency($total);
        $this->created_at_formatted = $created_at->translatedFormat('d F Y, h:i');
    }
}
