<?php

namespace App\Models;

use App\Notifications\SbmsResetPassword;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\RoutesNotifications;

class Customer extends Authenticatable
{
    use RoutesNotifications;

    protected $table = 'customer';
    protected $primaryKey = 'customerID';

    const CREATED_AT = 'createdAt';
    const UPDATED_AT = null;

    protected $fillable = [
        'firstName', 'middleName', 'lastName', 'email', 'password', 'contactNumber',
        'home_address', 'accountStatus', 'is_verified',
    ];

    protected $hidden = ['password', 'reset_token', 'otp_code'];

    protected function casts(): array
    {
        return ['password' => 'hashed'];
    }


    public function getRememberTokenName(): string
    {
        return '';
    }

    public function getFullNameAttribute(): string
    {
        return trim($this->firstName . ' ' . $this->lastName);
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'customerID', 'customerID');
    }

    public function sendPasswordResetNotification($token): void
    {
        $this->notify(new SbmsResetPassword($token, 'customers'));
    }
}
