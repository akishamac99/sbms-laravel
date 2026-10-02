<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceCategory extends Model
{
    protected $table = 'service_categories';
    protected $primaryKey = 'catID';

    const UPDATED_AT = null;

    protected $fillable = ['category_name', 'description', 'is_event_type'];

    public function services()
    {
        return $this->hasMany(Service::class, 'catID', 'catID');
    }
}
