<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'description',
    ];

    // Quan hệ: Permission -> Role (n-n)
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }
}
