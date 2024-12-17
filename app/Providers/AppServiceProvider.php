<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Country;
use App\Models\Year;
use App\Models\Movie;
use Illuminate\Support\Facades\Cache;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $category = Category::orderBy('id','ASC')->where('status',1)->get();
        $genre = Genre::orderBy('id','asc')->where('status',1)->get();
        $country = Country::orderBy('id','asc')->where('status',1)->get();
        $year = Year::orderBy('id','desc')->where('status',1)->get();
        // $topview = Movie::where('topview',1)->where('status',1)->orderBy('created_at','DESC')-> take('5')->get();
        // $new_comment = Movie::where('new_comment',1)->where('status',1)->orderBy('created_at','DESC')-> take('5')->get();
        // $topview = Cache::remember('topview_movies', 60, function () {
        //     return Movie::where('topview', 1)
        //         ->where('status', 1)
        //         ->orderBy('created_at', 'DESC')
        //         ->take(5)
        //         ->get();
        // });

        $topview = Movie::where('topview', 1)
            ->where('status', 1)
            ->orderBy('created_at', 'DESC')
            ->take(5)
            ->get();
            
        $new_comment = Movie::where('new_comment', 1)
                ->where('status', 1)
                ->orderBy('created_at', 'DESC')
                ->take(5)
                ->get();
        
    
        
        View::share([
            'category_home' => $category,
            'genre_home' => $genre,
            'country_home' => $country,
            'year_home' => $year,
            'topview' => $topview,
            'new_comment' => $new_comment
        ]);
    }
}
