<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class PaymentData extends Data
{
    #[Computed]
    public string $hash;

    public function __construct(
        public string $driver,
        public string $method,
        public string $label,
        public array $payload
    ) {
        $this->hash = md5("{$this->driver}|{$method}|" . json_encode($payload));
    }
}
