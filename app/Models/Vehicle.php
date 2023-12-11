<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Vehicle extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'vehicles';
    protected $dates = ['deleted_at'];
    protected $fillable = ['number_plate', 'merk', 'type', 'max_tonase'];

    public function deliveryOrder()
    {
        return $this->hasMany(DeliveryOrder::class, 'vehicle_id');
    }
}
