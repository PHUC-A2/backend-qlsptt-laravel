<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles');
    }

    /**
     * Lấy tất cả permission name của user (từ các role).
     * Trả về Collection các tên permission (string)
     */
    public function getAllPermissions()
    {
        // load roles -> permissions, chuyển về tên duy nhất
        return $this->roles()
            ->with('permissions')
            ->get()
            ->pluck('permissions')   // collection of collections
            ->flatten()             // flatten to single collection
            ->pluck('name')         // get only names
            ->unique()
            ->values();
    }

    /**
     * Kiểm tra user có permission (tên) hay không
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->getAllPermissions()->contains($permissionName);
    }
}
