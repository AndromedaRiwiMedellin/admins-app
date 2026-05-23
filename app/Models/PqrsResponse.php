<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PqrsResponse extends Model
{
    protected $table = 'pqrs_responses';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'pqrs_id',
        'employee_id',
        'response',
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function pqrs()
    {
        return $this->belongsTo(Pqrs::class, 'pqrs_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }
}