<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Region;
use App\Data\RegionData;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelData\DataCollection;

class RegionQueryService
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }

    public function searchRegionByName(string $keyword, int $limit = 5): DataCollection
    {
        $regions = Region::village()
            ->where(function ($query) use ($keyword) {
                $query
                    ->whereLike('name', "%$keyword%")
                    ->orWhereLike('postal_code', "%$keyword%")
                    ->orWhereHas('parent', function ($query) use ($keyword) {
                        $query->whereLike('name', "%$keyword%");
                    })
                    ->orWhereHas('parent.parent', function ($query) use ($keyword) {
                        $query->whereLike('name', "%$keyword%");
                    })
                    ->orWhereHas('parent.parent.parent', function ($query) use ($keyword) {
                        $query->whereLike('name', "%$keyword%");
                    });
            })
            ->with('parent.parent.parent')
            ->limit($limit)
            ->get();

        return new DataCollection(RegionData::class, $regions);
    }

    public function searchRegionByCode(string $code): ?RegionData
    {
        return RegionData::fromModel(Region::where('code', $code)->first());
    }
}
