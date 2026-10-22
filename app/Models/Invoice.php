<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $table = 'invoices';

    public $timestamps = false;

    protected $fillable = [
        'contract_id',
        'rent_amount',
        'month',
        'electricity',
        'water',
        'service_fee',
        'other_fee',
        'total',
        'status',
        'due_date',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'electricity' => 'decimal:2',
        'water' => 'decimal:2',
        'service_fee' => 'decimal:2',
        'other_fee' => 'decimal:2',
        'rent_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'due_date' => 'date',
        'paid_at' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }
}
