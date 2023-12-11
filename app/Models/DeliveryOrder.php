<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryOrder extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'delivery_orders';
    protected $fillable = ['admin_id', 'driver_id', 'origin_location_name', 'destination_location_name', 'vehicle_id', 'type_of_load', 'initial_tonnage', 'final_tonnage', 'information', 'status', 'image_proof_of_payment'];

    public function driver()
    {
        return $this->belongsTo(User::class, "id");
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'id');
    }
}
