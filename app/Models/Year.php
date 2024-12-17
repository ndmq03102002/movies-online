<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Year extends Model
{
    use HasFactory;
    protected $table = 'years';
    protected $fillable = ['name', 'description','slug', 'status'];
    public function movie()
    {
        return $this->hasMany(Movie::class)->orderBy('id','DESC'); // để hiển thị phim theo thứ tự mới nhất
    }
}
