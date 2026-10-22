<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $table = 'properties';

    protected $fillable = [
        'name',
        'address',
        'phone',
        'description',
        'status',
    ];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function tenants()
    {
        return $this->hasMany(Tenant::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function managers()
    {
        return $this->hasMany(User::class, 'property_id')
            ->where('role', 'manager');
    }
}
