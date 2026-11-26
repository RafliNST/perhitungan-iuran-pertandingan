<?php

namespace App\Models;

use App\Enum\ItemDistributionType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;
    
    protected $table = 'items';
    protected $primaryKey = 'id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = true;
    protected $fillable = [
        'event_id',
        'name',
        'price',
        'distribution_type',
    ];
    protected $casts = [
        'distribution_type' => ItemDistributionType::class,
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function expenses()
    {
        return $this->hasMany(ItemExpense::class, 'item_id');
    }
}
