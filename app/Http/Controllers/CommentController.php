<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request,  $movie_id ,$episode_id = null)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment = new Comment();
        $comment->content = $request->input('content');
        $comment->user_id = Auth::id();
        $comment->movie_id = $movie_id;
        $comment->episode_id = $episode_id;
        $comment->save();

        return redirect()->back()->with('success', 'Comment added successfully!');
    }

    public function edit($id)
    {
        $comment = Comment::findOrFail($id);
        return response()->json($comment);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'content' => 'required|string|max:255',
        ]);

        $comment = Comment::findOrFail($id);
        $comment->content = $request->input('content');
        $comment->save();

        // Nếu bạn muốn quay lại view, bạn cần truyền lại dữ liệu cần thiết
        return redirect()->back()->with('success', 'Comment updated successfully!');
    }

    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return response()->json(['success' => 'Comment deleted successfully!']);
    }
}
