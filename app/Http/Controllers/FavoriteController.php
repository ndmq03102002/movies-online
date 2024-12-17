<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Favorite;
use Illuminate\Support\Facades\Auth;
use App\Models\Movie;


class FavoriteController extends Controller
{
    // Lưu phim yêu thích
    public function store(Request $request, $slug)
    {
        
        $userId = Auth::id();
        $movieSlug = $slug;

        // Kiểm tra nếu phim đã có trong danh sách yêu thích
        $favorite = Favorite::where('user_id', $userId)
                            ->where('movie_slug', $movieSlug)
                            ->first();

        if ($favorite) {
            return redirect()->back()->with('error', 'Phim đã có trong danh sách yêu thích.');
        }

        // Lưu phim yêu thích
        Favorite::create([
            'user_id' => $userId,
            'movie_slug' => $movieSlug,
        ]);

        return redirect()->back()->with('success', 'Đã thêm phim vào danh sách yêu thích.');
    }

    // Lấy danh sách phim yêu thích của người dùng
    public function index()
    {
        $userId = Auth::id();
        $favorites = Favorite::where('user_id', $userId)->get();

        return response()->json($favorites);
    }

    public function showFavorites()
{
    $userId = Auth::id();
    $favorites = Favorite::where('user_id', $userId)->pluck('movie_slug');
    $movies = Movie::whereIn('slug', $favorites)->with(['episode.comments'])->paginate(9);

    return view('pages.favorites', compact('movies', 'favorites'));
}


    // Xóa phim yêu thích
    public function destroy($slug)
{
    $userId = Auth::id();

    $favorite = Favorite::where('user_id', $userId)
                        ->where('movie_slug', $slug)
                        ->first();

    if ($favorite) {
        $favorite->delete();
        return redirect()->back()->with('success', 'Đã xóa phim khỏi danh sách yêu thích.');
    }

    return redirect()->back()->with('error', 'Phim không có trong danh sách yêu thích.');
}

}
