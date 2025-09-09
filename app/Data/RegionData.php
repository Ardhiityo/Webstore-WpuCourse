<?php

declare(strict_types=1);

namespace App\Data;

use App\Models\Region;
use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class RegionData extends Data
{
    #[Computed]
    public string $label;

    public function __construct(
        public string $code,
        public string $province,
        public string $city,
        public string $district,
        public string $sub_district,
        public string|null $postal_code,
        public string $country = 'indonesia'
    ) {
        $this->label = "{$sub_district}, {$district}, {$city}, {$province}, {$postal_code}";
    }

    public static function fromModel(Region $region): self
    {
        return new self(
            $region->code,
            $region->parent->parent->parent->name,
            $region->parent->parent->name,
            $region->parent->name,
            $region->name,
            $region->postal_code,
        );
    }
}
