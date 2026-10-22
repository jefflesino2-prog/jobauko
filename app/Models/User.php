<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    protected $fillable = [
        'name', 'email', 'password', 'phone', 'barangay', 'education', 'skills', 'identity', 'interests', 'is_admin',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'interests' => 'array',
            'is_admin' => 'boolean',
        ];
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function savedOpportunities()
    {
        return $this->belongsToMany(Opportunity::class, 'bookmarks')->withTimestamps();
    }

    public function notices()
    {
        return $this->hasMany(Notice::class);
    }
}
