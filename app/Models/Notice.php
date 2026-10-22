<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    protected $fillable = ['user_id', 'kind', 'text', 'priority', 'read_at'];

    protected function casts(): array
    {
        return ['priority' => 'boolean', 'read_at' => 'datetime'];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
