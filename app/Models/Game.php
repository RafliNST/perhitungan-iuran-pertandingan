<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;
    
    protected $table = 'games';
    protected $primaryKey = 'id';
    public $incrementing = true;
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
