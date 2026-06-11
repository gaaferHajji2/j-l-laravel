<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    public function scopeOfType(mixed $query, string $type): mixed
    {
        return $query->where('type', $type)->where('description', 'LIKE', '%something%')->orderBy('date')->limit(5);
    }
}
