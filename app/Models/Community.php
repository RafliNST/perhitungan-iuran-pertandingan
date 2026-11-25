<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Override;

class Community extends Model
{
    use HasFactory;
    
    protected $table = 'communities';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_public',
        'banner_url',
        'owner_id',
    ];

    protected function casts() {
        return [
            'is_public' => 'boolean'
        ];
    }

    #[Override]
    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function members()
    {
        return $this->belongsToMany(User::class, 'community_members', 'community_id', 'user_id');
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'community_id');
    }
}
