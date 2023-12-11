<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

    protected $table = 'users';
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'role',
        'status',
        'avatar',
        'password'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function driver()
    {
        return $this->hasOne(Driver::class, 'user_id');
    }

    public function adminDeliveryOrder()
    {
        return $this->hasMany(DeliveryOrder::class, 'admin_id');
    }

    public function driverDeliveryOrder()
    {
        return $this->hasMany(DeliveryOrder::class, 'driver_id');
    }
}
