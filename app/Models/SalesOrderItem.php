<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalesOrderItem extends Model
{
    public function SalesOrder(): BelongsTo
    {
        return $this->belongsTo(SalesOrder::class);
    }
}
