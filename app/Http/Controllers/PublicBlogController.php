<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PublicBlogController extends Controller
{
    public function index()
    {
        $posts = Post::where('status', 'published')
            ->latest('published_at')
            ->paginate(9);

        return view('blog.index', compact('posts'));
    }

    public function show(Post $post)
    {
        if ($post->status !== 'published') {
            abort(404);
        }

        $relatedPosts = Post::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->latest('published_at')
            ->take(3)
            ->get();
        
        return view('blog.show', compact('post', 'relatedPosts'));
    }
}
