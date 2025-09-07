<?php

declare(strict_types=1);

namespace App\Contract;

use App\Data\CartData;
use App\Data\CartItemData;
use Illuminate\Support\Collection;

interface CartServiceInterface
{
    public function addOrUpdate(CartItemData $item): void;
    public function getItemBySku(string $sku): ?CartItemData;
    public function remove(string $sku): void;
    public function save(Collection $item): void;
    public function all(): CartData;
}
