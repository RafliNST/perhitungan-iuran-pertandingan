<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $table = 'games';
    protected $primaryKey = ['team_id', 'event_id', 'community_id', 'user_id'];
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'team_id',
        'event_id',
        'community_id',
        'user_id',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function community()
    {
        return $this->belongsTo(Community::class, 'community_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
