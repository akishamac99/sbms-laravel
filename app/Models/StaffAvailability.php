<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaffAvailability extends Model
{
    protected $table = 'staff_availability';
    protected $primaryKey = 'availID';

    public $timestamps = false;

    protected $guarded = ['availID'];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffID', 'staffID');
    }
}
