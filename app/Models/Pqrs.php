<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pqrs extends Model
{
    protected $table = 'pqrs';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    const TYPES = [
        'P' => 'Pregunta',
        'Q' => 'Queja',
        'R' => 'Reclamo',
        'S' => 'Sugerencia',
    ];

    const STATUSES = [
        'pending'    => 'Pendiente',
        'in_process' => 'En gestión',
        'resolved'   => 'Resuelto',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function responses()
    {
        return $this->hasMany(PqrsResponse::class, 'pqrs_id');
    }

    public function getTypeLabel()
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getStatusLabel()
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }
}