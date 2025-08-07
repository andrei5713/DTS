<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name',
        'username',
        'email',
        'password',
        'role',
        'unit_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isDepartment()
    {
        return $this->role === 'department';
    }

    public function isDepartmentHead()
    {
        return $this->role === 'department_head';
    }

    public function isUser()
    {
        return $this->role === 'user';
    }
}
