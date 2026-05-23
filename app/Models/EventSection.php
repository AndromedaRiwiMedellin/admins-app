<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EventSection extends Model
{
    protected $table = 'event_sections';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'section_name',
        'price',
        'capacity',
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