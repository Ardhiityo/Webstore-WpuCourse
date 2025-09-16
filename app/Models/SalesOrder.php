<?php

namespace App\Models;

use App\States\SalesOrder\SalesOrderState;
use Spatie\ModelStates\HasStates;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalesOrder extends Model
{
    use HasStates;

    protected $with = [
        'status' => SalesOrderState::class,
        'items'
    ];

    protected $casts = [
        'payment_payload' => 'json'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(SalesOrderItem::class);
    }
}
