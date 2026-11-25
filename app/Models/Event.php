<?php

namespace App\Models;

use App\Enum\EventStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    
    protected $table = 'events';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'location',
        'start_time',
        'end_time',
        'community_id',
        'host_id',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'status' => EventStatus::class,
    ];

    public function community()
    {
        return $this->belongsTo(Community::class, 'community_id');
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function participants()
    {
        return $this->hasMany(Participant::class, 'event_id');
    }
}
