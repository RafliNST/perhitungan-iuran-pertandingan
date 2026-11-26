<?php

namespace App\Models;

use App\Enum\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;
    
    protected $table = 'participants';
    protected $primaryKey = ['event_id', 'user_id'];
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'event_id',
        'user_id',
        'payment_status',
        'fee_total',
    ];

    protected $casts = [
        'payment_status' => PaymentStatus::class,
        'fee_total' => 'decimal:2',
        'attended_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
