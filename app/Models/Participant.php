<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $table = 'participants';
    protected $primaryKey = ['event_id', 'user_id'];
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'event_id',
        'user_id',
        'payment_status',
        'fee_total',
    ];

    protected $casts = [
        'payment_status' => \App\Enum\PaymentStatus::class,
        'fee_total' => 'decimal:2',
    ];
}
