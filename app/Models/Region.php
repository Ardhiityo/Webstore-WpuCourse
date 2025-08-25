<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    public function parent()
    {
        return $this->belongsTo(Region::class, 'parent_code', 'code');
    }

    public function children()
    {
        return $this->hasMany(Region::class, 'parent_code', 'code');
    }

    public function scopeProvinces($query)
    {
        return $query->where('type', 'province');
    }

    public function scopeRegencies($query)
    {
        return $query->where('type', 'regency');
    }

    public function scopeDistricts($query)
    {
        return $query->where('type', 'district');
    }

    public function scopeVillages($query)
    {
        return $query->where('type', 'village');
    }
}
