<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SystemPolicy extends Model
{
    protected $table = 'system_policies';

    public $timestamps = false;

    protected $fillable = ['policy_key', 'policy_value', 'description'];

    // Read one setting, e.g. SystemPolicy::value('downpayment_percent', 20)
    public static function value(string $key, mixed $default = null): mixed
    {
        return static::where('policy_key', $key)->value('policy_value') ?? $default;
    }
}
