<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Driver extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'drivers';
    protected $fillable = ['user_id', 'alt_phone_number', 'address'];

    public function user()
    {
        return $this->belongsTo(User::class, 'id');
    }
}
