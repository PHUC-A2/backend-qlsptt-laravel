<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = ['name'];

    // Quan hệ: Role -> Permission (n-n)
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    // (không bắt buộc) Role -> User (n-n)
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }
}
