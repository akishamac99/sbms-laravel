<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'booking';
    protected $primaryKey = 'bookingID';

    protected $guarded = ['bookingID'];

    protected function casts(): array
    {
        return [
            'bookingDate' => 'date',
            'total_amount' => 'decimal:2',
            'downpayment_amount' => 'decimal:2',
            'discount_amount' => 'decimal:2',
        ];
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customerID', 'customerID');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staffID', 'staffID');
    }

    // Named serviceItem because the booking table already has a text column called "service".
    public function serviceItem()
    {
        return $this->belongsTo(Service::class, 'serviceID', 'serviceID');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'bookingID', 'bookingID');
    }

    public function feedback()
    {
        return $this->hasOne(Feedback::class, 'bookingID', 'bookingID');
    }
}
