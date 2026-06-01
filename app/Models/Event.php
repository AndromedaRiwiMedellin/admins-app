<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id',
        'title',
        'description',
        'poster_url',
        'event_date',
        'sale_start',
        'sale_end',
        'total_capacity',
        'created_by',
        'created_at',
    ];

    protected $casts = [
        'event_date' => 'datetime',
        'sale_start' => 'datetime',
        'sale_end'   => 'datetime',
        'created_at' => 'datetime',
    ];

    public function sections()
    {
        return $this->hasMany(EventSection::class, 'event_id');
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'event_id');
    }

    public function favorites()
    {
        return $this->hasMany(Favorite::class, 'event_id');
    }
}
