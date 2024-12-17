<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

use App\Models\Category;
use App\Models\Genre;
use App\Models\Country;
use App\Models\Movie;
use App\Models\Episode;
use App\Models\Movie_Genre;
use App\Models\Year;
use App\Models\Comment;
use App\Models\Rating;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Favorite;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;


class IndexController extends Controller
{
    public function index()
    {
        $config['method'] = 'homepage';

        // Lấy tất cả category và movie của nó, cùng với các episode (eager loading cho movie và episode)
        $category = Category::with('movie')->orderBy('id', 'ASC')->where('status', 1)->get();

        $phim_hot = Movie::where('phim_hot', 1)->where('status', 1)->orderBy('created_at', 'DESC')->take(5)->get();
        // $phim_hot = Cache::remember('phim_hot', 60, function () {
        //     return Movie::select('id', 'name', 'slug', 'description', 'poster') // Chọn các trường cần thiết
        //         ->where('phim_hot', 1)
        //         ->where('status', 1)
        //         ->orderBy('created_at', 'DESC')
        //         ->take(5)
        //         ->get();
        // });
        return view('pages.include.home', compact('config', 'category', 'phim_hot'));
    }

    public function category($slug)
    {
        $config['method'] = 'category';

        // Lấy category theo slug và trạng thái
        $cate_slug = Category::where('slug', $slug)->where('status', 1)->first();

        // Lấy tất cả các phim của category và eager load các tập phim cùng với comment
        $movies = Movie::where('category_id', $cate_slug->id)
            ->where('status', 1)
            ->with(['episode.comments'])  // Eager load episode và comments của từng tập
            ->orderBy('id', 'DESC')
            ->paginate(15);

        return view('pages.category', compact('config', 'cate_slug', 'movies'));
    }

    public function genre($slug)
    {
        $config['method'] = 'genre';
        $genre_slug = Genre::where('slug', $slug)->where('status', 1)->first();
        // nhiều thể loại
        $movie_genre = Movie_Genre::where('genre_id', $genre_slug->id)->get();
        $many_genre = [];
        foreach ($movie_genre as $mov) {
            $many_genre[] = $mov->movie_id;
        }
        $movies = Movie::whereIn('id', $many_genre)->where('status', 1)->with(['episode.comments'])->orderBy('id', 'DESC')->paginate(15);

        return view('pages.genre', compact('config', 'genre_slug', 'movies'));
    }
    public function country($slug)
    {
        $config['method'] = 'country';
        $country_slug = Country::where('slug', $slug)->where('status', 1)->first();
        $movies = Movie::with('category', 'genres', 'country', 'year', 'movie_genre')->where('country_id', $country_slug->id)->where('status', 1)->with(['episode.comments'])->orderBy('id', 'DESC')->paginate(15);

        return view('pages.country', compact('config', 'country_slug', 'movies'));
    }
    public function movie($slug)
    {
        $config['method'] = 'movie';
        $movie = Movie::with('category', 'genres', 'country', 'year', 'movie_genre', 'episode', 'comments')->where('slug', $slug)->where('status', 1)->with(['episode.comments'])->first();
        $related = Movie::where('category_id', $movie->category_id)
            ->where('status', 1)
            ->where('slug', '!=', $slug)
            ->orderBy('created_at', 'desc') // Sắp xếp theo ngày tạo mới nhất
            ->limit(5) // Giới hạn 5 kết quả
            ->get();

        if (isset($movie->episode->first()->episode)) {
            $tapdau = $movie->episode->first()->episode;
        } else {
            $tapdau = null;
        }

        $comments = Comment::where('movie_id', $movie->id)
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->get();
        $totalVotes = Rating::where('movie_id', $movie->id)->count();
        $ratings = Rating::where('movie_id', $movie->id)->avg('rating');
        $userId = Auth::id();
        $favorites = Favorite::where('user_id', $userId)->get();

        return view('pages.movie', compact('config', 'movie', 'related', 'comments',  'totalVotes', 'ratings', 'favorites', 'tapdau'));
    }
    public function watch($slug, $tap)
    {
        $config['method'] = 'watch';
        $movie = Movie::with('episode')->where('slug', $slug)->where('status', 1)->first();

        $movie->increment('count_views');

        // Lấy tất cả các tập của phim và sắp xếp
        $episodes = Episode::where('movie_id', $movie->id)
            ->orderBy(DB::raw('CAST(episode AS UNSIGNED)'), 'asc')
            ->get();

        // Lấy tập hiện tại dựa vào số tập
        $episode = Episode::where('movie_id', $movie->id)
            ->where('episode', $tap)
            ->first();

        $episode_id = $episode->id;

        $related = Movie::where('category_id', $movie->category_id)
            ->where('status', 1)
            ->where('slug', '!=', $slug)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Lấy các bình luận cho tập phim
        $comments = Comment::where('episode_id', $episode_id)
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('pages.watch', compact('config', 'movie', 'tap', 'episode', 'episodes', 'related', 'episode_id', 'comments'));
    }
    public function watch_trailer($slug)
    {
        $config['method'] = 'watch_trailer';
        $movie = Movie::where('slug', $slug)->where('status', 1)->first();

        $related = Movie::where('category_id', $movie->category_id)
            ->where('status', 1)
            ->where('slug', '!=', $slug)
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $comments = Comment::where('movie_id', $movie->id)
            ->with('user')
            ->orderBy('updated_at', 'desc')
            ->get();
        return view('pages.watch-trailer', compact('config', 'movie',  'related', 'comments'));
    }


    public function episode()
    {
        $config['method'] = 'episode';
        return view('pages.episode', compact('config'));
    }
    public function year($slug)
    {
        $config['method'] = 'year';
        $year_slug = Year::where('slug', $slug)->where('status', 1)->first();
        $movies = Movie::where('year_id', $year_slug->id)->where('status', 1)->with(['episode.comments'])->orderBy('id', 'DESC')->paginate(15);
        return view('pages.year', compact('config', 'year_slug', 'movies'));
    }

    public function search(Request $request)
{
    // Lấy từ khóa tìm kiếm
    $query = $request->input('query');

    // Tìm kiếm các bộ phim với từ khóa ở giữa
    $movies = Movie::where('name', 'like', '%' . $query . '%')
        ->orWhere('name_en', 'like', '%' . $query . '%')
        ->with(['episode.comments'])
        ->paginate(12);

    // Thêm query tìm kiếm vào đường dẫn của mỗi trang
    $movies->appends(['query' => $query]);

    // Trả về view với kết quả tìm kiếm
    return view('pages.search', [
        'movies' => $movies,
        'query' => $query,
    ]);
}

public function locphim(Request $request)
{
    // Lấy dữ liệu từ form
    $orderBy = $request->input('orderBy');
    $category = $request->input('category');
    $genre = $request->input('genre'); // Đây là ID của genre được truyền qua
    $country = $request->input('country');
    $year = $request->input('year');

    // Khởi tạo query để lọc phim
    $query = Movie::query();

    // Lọc theo danh mục (category)
    if ($category) {
        $query->where('movies.category_id', $category);
    }

    // Lọc theo quốc gia (country)
    if ($country) {
        $query->where('movies.country_id', $country);
    }

    // Lọc theo năm (year)
    if ($year) {
        $query->where('movies.year_id', $year);
    }

    // Lọc theo thể loại (genre)
    if ($genre) {
        // Truy vấn bảng movie_genre để lấy danh sách movie_id có genre_id tương ứng
        $movieIds = DB::table('movie_genre')
            ->where('genre_id', $genre)
            ->pluck('movie_id');
        
        // Lọc phim theo danh sách movie_id
        $query->whereIn('movies.id', $movieIds);
    }

    // Sắp xếp theo yêu cầu (orderBy)
    if ($orderBy == 'UpdateOn') {
        $query->orderBy('movies.updated_at', 'desc');
    } elseif ($orderBy == 'ViewNumber') {
        $query->orderBy('movies.count_views', 'desc');
    } elseif ($orderBy == 'rating') {
        // Tính điểm rating trung bình từ bảng ratings và sắp xếp theo nó
        $query->leftJoin('ratings', 'movies.id', '=', 'ratings.movie_id')
            ->select(
                'movies.*', 
                DB::raw('AVG(ratings.rating) as average_rating')
            )
            ->groupBy(
                'movies.id', 
                'movies.name', 
                'movies.updated_at', 
                'movies.count_views', 
                'movies.category_id', 
                'movies.country_id', 
                'movies.year_id'
            )
            ->orderBy('average_rating', 'desc');
    }

    // Phân trang kết quả, 12 phim mỗi trang
    $movies = $query->paginate(12);

    // Trả về view với dữ liệu phim đã lọc và phân trang
    // Thêm các tham số vào phân trang
    return view('pages.locphim', compact('movies'))
        ->with('orderBy', $orderBy)
        ->with('category', $category)
        ->with('genre', $genre)
        ->with('country', $country)
        ->with('year', $year);
}





}
