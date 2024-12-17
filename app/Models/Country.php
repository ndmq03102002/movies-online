<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    protected $table = 'countries';
    protected $fillable = ['name', 'description','slug', 'status'];
    public function movie()
    {
        return $this->hasMany(Movie::class)->where('status',1)->orderBy('id','DESC'); // để hiển thị phim theo thứ tự mới nhất
    }
}
