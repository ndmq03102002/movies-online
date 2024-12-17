<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\Movie;
use App\Models\Movie_Genre;
use App\Models\Episode;
use App\Models\Comment;
use App\Models\Rating;
use App\Models\Favorite;
class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::all();
        return view('admin.country.index', compact('countries'));
    }

    
    public function create()
    {
        $config['method'] = 'create';
        return view('admin.country.create', compact('config'));
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
        Country::create($data);    
        return redirect()->route('country.create')->with('success', 'Thêm danh mục thành công.');
    }

    
    public function show(string $id)
    {
        //
    }

   
    public function edit(string $id)
    {
        $country = Country::find($id);
        $config['method'] = 'edit';
        return view('admin.country.create', compact('config','country'));
    }

   
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            
        ], [
            'name.required' => 'Tên danh mục là bắt buộc.',
            'name.string' => 'Tên danh mục phải là một chuỗi ký tự.',
            'name.max' => 'Tên danh mục không được vượt quá 255 ký tự.',
            
        ]);
        
        $data = $request->all();
   
        Country::find($id)->update($data);
        return redirect()->route('country.index')->with('success', 'Cập nhật danh mục thành công.');
    }

    
    public function destroy($id)
    {
        $movies = Movie::where('country_id', $id)->get();

        foreach ($movies as $movie) {
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
           
            // Xóa phim
            $movie->delete();
        }
        Country::find($id)->delete();
        return redirect()->back()->with('success', 'Xóa danh mục thành công.');
    }
}
