<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    protected $table = 'activities';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'title',
        'description',
        'host_id',
        'community_id',
        'start_time',
        'end_time',
        'activity_type',
        'created_at',
        'updated_at',
    ];

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function community()
    {
        return $this->belongsTo(Community::class, 'community_id');
    }
}
