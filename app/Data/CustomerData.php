<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Data;

class CustomerData extends Data
{
    public function __construct(
        public string $full_name,
        public string $email,
        public string $phone
    ) {}
}
