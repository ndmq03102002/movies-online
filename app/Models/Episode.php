<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Episode extends Model
{
    use HasFactory;
    protected $table = 'episodes';

    protected $fillable = ['movie_id', 'linkphim','episode', 'status'];
    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }
}
