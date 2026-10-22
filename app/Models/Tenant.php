<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenant extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $table = 'tenants';

    protected $fillable = [
        'property_id',
        'full_name',
        'phone',
        'cccd',
        'address',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }
}
