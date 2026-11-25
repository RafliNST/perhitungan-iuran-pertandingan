<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemExpense extends Model
{
    use HasFactory;
    
    protected $table = 'item_expenses';
    protected $primaryKey = ['item_id', 'user_id', 'event_id'];
    public $incrementing = false;
    public $timestamps = true;

    protected $fillable = [
        'item_id',
        'user_id',
        'event_id',
        'qty',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class, 'item_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}
