<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Genre;
use App\Models\Country;
use App\Models\Movie;
use App\Models\Episode;
use App\Models\Year;
use App\Models\Movie_Genre;
use App\Models\Comment;
use App\Models\Favorite;
use App\Models\Rating;
use Illuminate\Support\Facades\Http;

class MovieController extends Controller
{
    public function index()
    {
        // $movies = Movie::with('category', 'genres', 'country', 'year')->where('slug', 'tham-phan-den-tu-dia-nguc')->orderBy('id', 'desc')->get();

        // $movies = Movie::whereDoesntHave('episode')
        
        // ->orderBy('created_at', 'desc')
        // ->get();


        $movies = Movie::where(function ($query) {
        $query->where('sotap', '>', Episode::selectRaw('COUNT(*)')
            ->whereColumn('movie_id', 'movies.id'))
            ->orWhere('sotap', '?')->orWhere('sotap', '??');
        })->orderBy('created_at', 'desc')->get();

    //     $movies = Movie::where(function ($query) {
    //     $query->where('sotap', '<', Episode::selectRaw('COUNT(*)')
    //         ->whereColumn('movie_id', 'movies.id'))
    //         ->whereNotIn('sotap', ['?', '??']);
    // })->orderBy('created_at', 'desc')->get();


        $categories = Category::all(); // Lấy toàn bộ danh mục với đầy đủ các cột

        return view('admin.movie.index', compact('movies', 'categories'));
    }
    
    public function create()
    {
        $config['method'] = 'create';
        $category = Category::pluck('name', 'id');
        $genre = Genre::pluck('name', 'id');
        $country = Country::orderBy('name', 'asc')->pluck('name', 'id');
        $year = Year::orderBy('name', 'desc')->pluck('name', 'id');
        return view('admin.movie.create', compact('config','category','genre','country','year'));
    }

  
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.string' => 'Tên danh mục phải là một chuỗi ký tự.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            
        ]);
        $data = $request->all();

        $movie = new Movie();
        $movie->name = $data['name'];
        $movie->trailer = $data['trailer'];
        $movie->sotap = $data['sotap'];
        $movie->tags = $data['tags'];
        $movie->season = $data['season'];
        $movie->thoiluong = $data['thoiluong'];
        $movie->count_views = $data['count_views'];
        $movie->quality = $data['quality'];
        $movie->phude = $data['phude'];
        $movie->name_en = $data['name_en'];
        $movie->phim_hot = $data['phim_hot'];
        $movie->topview = $data['topview'];
        $movie->new_comment = $data['new_comment'];
        $movie->slug = $data['slug'];
        $movie->description = $data['description'];
        $movie->status = $data['status'];
        $movie->category_id = $data['category_id'];
        $movie->thuocphim = $data['thuocphim'];
        $movie->country_id = $data['country_id'];
        $movie->year_id = $data['year_id'];
        $movie->poster = $data['poster'];
        foreach($data['genre_id'] as $key => $gen){
            $movie->genre_id = $gen[0];
        }
        // $movie->image = $data['image'];
        // $get_image = $request->file('image');

        // if($get_image){
        //     $get_name_image = $get_image->getClientOriginalName(); //hinhanh1.jpg
        //     $name_image = current(explode('.',$get_name_image)); //[0] => hinhanh12624 , [1] => jpg
        //     $new_image = $name_image.rand(0,9999).'.'.$get_image->getClientOriginalExtension(); // hinhanh12624.jpg
        //     $get_image->move('uploads/movie/', $new_image);
        //     $movie->image = $new_image;
        // }

        if ($request->input('image_option') == 'upload' && $request->hasFile('image')) {
            // Xử lý khi người dùng chọn tải ảnh từ máy
            $get_image = $request->file('image');
            $get_name_image = $get_image->getClientOriginalName();
            $name_image = current(explode('.', $get_name_image));
            $new_image = $name_image . rand(0, 9999) . '.' . $get_image->getClientOriginalExtension();
            $get_image->move('uploads/movie/', $new_image);
            $movie->image = $new_image;
        } elseif ($request->input('image_option') == 'url' && !empty($request->input('image_url'))) {
            // Xử lý khi người dùng chọn nhập URL
            $movie->image = $request->input('image_url');
        }
        // Lưu phim mới
        $movie->save();
        
        // Thêm các thể loại được chọn bằng attach (vì là create)
        if ($request->has('genre_id')) {
            $movie->genres()->attach($request->input('genre_id'));
        }

        return redirect()->route('movie.create')->with('success', 'Thêm phim thành công');
    }

    
    
    public function show($slug) // hiển thị thông tin phim qua api
    {
        $url = "https://ophim1.com/phim/{$slug}";
        
        $response = Http::get($url);
        
        if ($response->successful() && $response['status']) {
            $movie = $response['movie'];

            // Lấy thông tin cần thiết
            $data = [
                'name' => $movie['name'],
                'type' => $movie['type'],
                'content' => $movie['content'],
                'trailer_url' => $movie['trailer_url'],
                'time' => $movie['time'],
                'episode_total' => $movie['episode_total'],
                'quality' => $movie['quality'],
                'lang' => $movie['lang'],
                'year' => $movie['year'],
                'categories' => array_map(fn($category) => $category['name'], $movie['category']),
                'countries' => array_map(fn($country) => $country['name'], $movie['country']),
            ];
            $linkEmbed = isset($response['episodes'][0]['server_data'][0]['link_embed']) 
            ? $response['episodes'][0]['server_data'][0]['link_embed'] 
            : null;

            $data['link_embed'] = $linkEmbed; // Thêm link_embed vào dữ li

            return view('admin.movie.show', compact('data'));
        } else {
            // Xử lý lỗi nếu không tìm thấy phim
            return redirect()->back()->with('error', 'Phim không tồn tại.');
        }
    }

   
    public function edit(string $id)
    {
        $movie = Movie::find($id);
        $category = Category::pluck('name', 'id');
        $genre = Genre::pluck('name', 'id');
        $country = Country::orderBy('name', 'asc')->pluck('name', 'id');
        $year = Year::orderBy('name', 'desc')->pluck('name', 'id');
        $config['method'] = 'edit';
        return view('admin.movie.create', compact('config','movie','category','genre','country','year'));
    }

   
    public function update(Request $request, $id)
    {
        if ($request->ajax()) {
            $movie = Movie::find($id);
    
            // Cập nhật danh mục
            if ($request->has('category_id')) {
                $movie->category_id = $request->category_id;
            }
    
            // Cập nhật trạng thái
            if ($request->has('status')) {
                $movie->status = $request->status;
            }
    
            // Cập nhật topview
            if ($request->has('topview')) {
                $movie->topview = $request->topview;
            }
    
            // Cập nhật new_comment
            if ($request->has('new_comment')) {
                $movie->new_comment = $request->new_comment;
            }
    
            // Cập nhật phim_hot
            if ($request->has('phim_hot')) {
                $movie->phim_hot = $request->phim_hot;
            }
    
            $movie->save();
    
            return response()->json(['success' => 'Thông tin đã được cập nhật']);
        }else{
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.string' => 'Tên danh mục phải là một chuỗi ký tự.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            
        ]);
        
        $data = $request->all();

        $movie = Movie::find($id);
        $movie->name = $data['name'];
        $movie->trailer = $data['trailer'];
        $movie->sotap = $data['sotap'];
        $movie->tags = $data['tags'];
        $movie->season = $data['season'];
        $movie->thoiluong = $data['thoiluong'];
        $movie->count_views = $data['count_views'];
        $movie->quality = $data['quality'];
        $movie->phude = $data['phude'];
        $movie->name_en = $data['name_en'];
        $movie->phim_hot = $data['phim_hot'];
        $movie->topview = $data['topview'];
        $movie->new_comment = $data['new_comment'];
        $movie->slug = $data['slug'];
        $movie->description = $data['description'];
        $movie->status = $data['status'];
        $movie->category_id = $data['category_id'];
        $movie->thuocphim = $data['thuocphim'];
        $movie->country_id = $data['country_id'];
        $movie->year_id = $data['year_id'];
        $movie->poster = $data['poster'];

        // $movie->image = $data['image'];
        foreach($data['genre_id'] as $key => $gen){
            $movie->genre_id = $gen[0];
        }
        // $get_image = $request->file('image');
        // if($get_image){
        //     if(!empty($movie->image) && file_exists('uploads/movie/'.$movie->image) ){ // file_exists kiểm tra file có tồn tại hay không
        //         unlink('uploads/movie/'.$movie->image);
        //     }
        //     $get_name_image = $get_image->getClientOriginalName(); //hinhanh1.jpg
        //     $name_image = current(explode('.',$get_name_image)); //[0] => hinhanh12624 , [1] => jpg
        //     $new_image = $name_image.rand(0,9999).'.'.$get_image->getClientOriginalExtension(); // hinhanh12624.jpg
        //     $get_image->move('uploads/movie/', $new_image);
        //     $movie->image = $new_image;
        // }

        if ($request->input('image_option') == 'upload' && $request->hasFile('image')) {
                if(!empty($movie->image) && file_exists('uploads/movie/'.$movie->image) ){ // file_exists kiểm tra file có tồn tại hay không
                unlink('uploads/movie/'.$movie->image);
            }
            // Xử lý khi người dùng chọn tải ảnh từ máy
            $get_image = $request->file('image');
            $get_name_image = $get_image->getClientOriginalName();
            $name_image = current(explode('.', $get_name_image));
            $new_image = $name_image . rand(0, 9999) . '.' . $get_image->getClientOriginalExtension();
            $get_image->move('uploads/movie/', $new_image);
            $movie->image = $new_image;
        } elseif ($request->input('image_option') == 'url' && !empty($request->input('image_url'))) {
            if(!empty($movie->image) && file_exists('uploads/movie/'.$movie->image) ){ // file_exists kiểm tra file có tồn tại hay không
                unlink('uploads/movie/'.$movie->image);
            }
            // Xử lý khi người dùng chọn nhập URL
            $movie->image = $request->input('image_url');
        }
        // Chỉ lưu nếu có thay đổi
        
            $movie->save();
            $movie->genres()->sync($data['genre_id']);
            return redirect()->route('movie.index')->with('success', 'Cập nhật phim thành công');
            }
        
    }

    
    public function destroy($id)
    {
        $movie = Movie::find($id);
        
        // Xóa ảnh nếu có
        if (!empty($movie->image) && file_exists('uploads/movie/' . $movie->image)) {
            unlink('uploads/movie/' . $movie->image);
        }

        // Xóa mối quan hệ genre
        Movie_Genre::where('movie_id', $movie->id)->delete();

        // Lấy tất cả các tập phim liên quan đến bộ phim này
        $episodes = Episode::where('movie_id', $movie->id)->pluck('id');

        // Xóa tất cả comment liên quan đến các tập phim này
        Comment::whereIn('episode_id', $episodes)->delete();

        // Xóa các đánh giá liên quan đến các tập phim này
        Rating::where('movie_id', $movie->id)->delete();

        // Xóa tất cả các tập phim
        Episode::where('movie_id', $movie->id)->delete();

        // Xóa tất cả các favorite liên quan đến bộ phim này
        Favorite::where('movie_slug', $movie->slug)->delete();
        
        // Cuối cùng, xóa bộ phim
        $movie->delete();

        return redirect()->back()->with('success', 'Xóa danh mục thành công.');
    }

// xóa phim trùng khi lỗi api
    public function deleteDuplicateMovies()
{
    // Lấy 500 phim mới cập nhật
    $movies = Movie::orderBy('id', 'DESC')->get();

    // Tạo một mảng để lưu slug đã gặp
    $slugs = [];

    // Lặp qua từng phim
    foreach ($movies as $movie) {
        if (in_array($movie->slug, $slugs)) {
            // Nếu slug đã tồn tại, xóa phim này
            // Xóa ảnh nếu có
            if (!empty($movie->image) && file_exists('uploads/movie/' . $movie->image)) {
                unlink('uploads/movie/' . $movie->image);
            }

            // Xóa mối quan hệ genre
            Movie_Genre::where('movie_id', $movie->id)->delete();

            // Lấy tất cả các tập phim liên quan đến bộ phim này
            $episodes = Episode::where('movie_id', $movie->id)->pluck('id');

            // Xóa tất cả comment liên quan đến các tập phim này
            Comment::whereIn('episode_id', $episodes)->delete();

            // Xóa các đánh giá liên quan đến các tập phim này
            Rating::where('movie_id', $movie->id)->delete();

            // Xóa tất cả các tập phim
            Episode::where('movie_id', $movie->id)->delete();

            // Xóa tất cả các favorite liên quan đến bộ phim này
            Favorite::where('movie_slug', $movie->slug)->delete();
            
            // Xóa bộ phim
            $movie->delete();
        } else {
            // Nếu slug chưa tồn tại, thêm vào mảng slug
            $slugs[] = $movie->slug;
        }
    }

    return redirect()->back()->with('success', 'Đã xóa tất cả phim trùng lặp, chỉ giữ lại một phim cho mỗi slug.');
}


// Xóa phim có 0 tập
public function destroyMoviesWithoutEpisodes()
{
    // Lấy tất cả các phim chưa có tập nào
    $movies = Movie::whereDoesntHave('episode')->get();

    foreach ($movies as $movie) {
        // Xóa ảnh nếu có
        if (!empty($movie->image) && file_exists('uploads/movie/' . $movie->image)) {
            unlink('uploads/movie/' . $movie->image);
        }

        // Xóa mối quan hệ genre
        Movie_Genre::where('movie_id', $movie->id)->delete();

        // Xóa tất cả các đánh giá liên quan đến bộ phim này
        Rating::where('movie_id', $movie->id)->delete();

        // Xóa tất cả các favorite liên quan đến bộ phim này
        Favorite::where('movie_slug', $movie->slug)->delete();
        
        // Cuối cùng, xóa bộ phim
        $movie->delete();
    }

    return redirect()->back()->with('success', 'Đã xóa tất cả phim chưa có tập nào.');
}

public function destroyMany(Request $request)
{
    // Lấy danh sách các ID phim từ request
    $movieIds = explode(',', $request->input('movie_ids'));

    // Lặp qua từng ID để xóa
    foreach ($movieIds as $id) {
        $this->destroy($id); // Gọi hàm destroy mà bạn đã viết trước đó
    }

    return redirect()->back()->with('success', 'Đã xóa các phim đã chọn.');
}
    
}