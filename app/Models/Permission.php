<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $table = 'permissions';

    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['name'];

    public function employees()
    {
        return $this->belongsToMany(
            Employee::class,
            'employee_permissions',
            'permission_id',
            'employee_id'
        );
    }
}