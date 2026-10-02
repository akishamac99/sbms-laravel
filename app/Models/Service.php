<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $table = 'service';
    protected $primaryKey = 'serviceID';

    const UPDATED_AT = null;

    protected $fillable = [
        'serviceName', 'catID', 'servicePrice', 'duration', 'description', 'image',
        'is_home_service', 'is_active',
    ];

    protected function casts(): array
    {
        return ['servicePrice' => 'decimal:2'];
    }

    public function category()
    {
        return $this->belongsTo(ServiceCategory::class, 'catID', 'catID');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
