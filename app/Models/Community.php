<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Community extends Model
{
    protected $table = 'communities';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'name',
        'slug',
        'description',
        'banner_url',
        'owner_id',
        'created_at',
        'updated_at',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
