<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rating;

class RatingController extends Controller
{
    public function store(Request $request)
{
    // Xác thực yêu cầu
    $request->validate([
        'movie_id' => 'required|exists:movies,id',
        'rating' => 'required|integer|min:1|max:5',
    ]);

    // Lưu hoặc cập nhật đánh giá
    $rating = Rating::where('movie_id', $request->movie_id)
                    ->where('user_id', auth()->id())
                    ->first();

    if ($rating) {
        // Nếu đã có đánh giá trước đó, cập nhật
        $rating->update(['rating' => $request->rating]);
    } else {
        // Nếu chưa có đánh giá, tạo mới
        Rating::create([
            'movie_id' => $request->movie_id,
            'user_id' => auth()->id(),
            'rating' => $request->rating,
        ]);
    }

    return response()->json([
        'success' => true,
    ]);
}


}
