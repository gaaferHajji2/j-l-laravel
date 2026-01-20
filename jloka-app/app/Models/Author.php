<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Author extends Model
{
    protected $primaryKey = 'author_slug';
    public $incrementing = false;
    
    public function books()
    {
        return $this->hasMany(Book::class, 'writer_id', 'author_slug');
    }
}