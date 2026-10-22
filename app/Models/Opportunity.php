<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    protected $fillable = ['title', 'provider', 'category', 'location', 'deadline', 'eligibility', 'description', 'status'];

    protected function casts(): array
    {
        return ['deadline' => 'date'];
    }

    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function daysLeft(): int
    {
        return (int) now()->startOfDay()->diffInDays($this->deadline->copy()->startOfDay(), false);
    }

    public function isExpired(): bool
    {
        return $this->daysLeft() < 0;
    }
}
