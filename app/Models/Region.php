<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public $timestamps = false;

    public function parent()
    {
        return $this->belongsTo(Region::class, 'parent_code', 'code');
    }

    public function children()
    {
        return $this->hasMany(Region::class, 'parent_code', 'code');
    }

    public function scopeProvince(Builder $query)
    {
        return $query->where('type', 'province');
    }

    public function scopeDistrict(Builder $query)
    {
        return $query->where('type', 'district');
    }

    public function scopeVillage(Builder $query)
    {
        return $query->where('type', 'village');
    }
}
