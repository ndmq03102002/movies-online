<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Genre;
use App\Models\Country;
use App\Models\Year;
use App\Models\Category;


class Movie extends Model
{
    use HasFactory;
    protected $table = 'movies';
    protected $fillable = ['name', 'description','slug', 'status','category_id','genre_id','country_id','year_id','topview','new_comment','phim_hot','name_en','image','poster','quality','trailer','phude','tags','season','thoiluong', 'sotap', 'thuocphim', 'count_views', 'source'];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'movie_genre', 'movie_id', 'genre_id');
    }
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    public function year()
    {
        return $this->belongsTo(Year::class);
    }

    public function movie_genre()
    {
        return $this->belongsToMany(Genre::class, 'movie_genre', 'movie_id', 'genre_id');
    }

    public function episode()
    {
        return $this->hasMany(Episode::class)->orderBy('episode', 'asc');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function ratings()
    {
        return $this->hasMany(Rating::class, 'movie_id', 'id');
    }


}

/*
slug: đuôi của url
status: trạng thái phim
category_id: id của thể loại
genre_id: id của thể loại phim
country_id: id của quốc gia
year_id: id của năm sản xuất
topview: có hoặc không để đặt phim nổi bật
new_comment: có hoặc không để đặt phim mới
phim_hot: có hoặc không để đặt phim hot
name_en: tên phim tiếng anh
image: ảnh đại diện
quality: chất lượng phim
trailer: link trailer
phude: phụ đề vietsub , thuyết minh
tags: tag của phim
season: số mùa
thoiluong: thời lượng
sotap: số tập
thuocphim: thuộc phim nào : phim lẻ, phim bộ
count_view: số lượt xem

*/