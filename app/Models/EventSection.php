<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSection extends Model
{
    protected $table = 'event_area';

    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'area_name',
        'price',
        'capacity',
        'description',
    ];

    protected $casts = [
        'price'    => 'float',
        'capacity' => 'integer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }
}