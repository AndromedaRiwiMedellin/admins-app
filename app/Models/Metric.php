<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Metric extends Model
{
    protected $table = 'metrics';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'metric_name',
        'metric_value',
        'recorded_at',
    ];

    protected $casts = [
        'metric_value' => 'float',
        'recorded_at'  => 'datetime',
    ];
}