<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'rooms';

    protected $fillable = [
        'property_id',
        'room_number',
        'price',
        'area',
        'status',
    ];

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
