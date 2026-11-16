<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityMember extends Model
{
    protected $table = 'community_members';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'community_id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function community()
    {
        return $this->belongsTo(Community::class, 'community_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
