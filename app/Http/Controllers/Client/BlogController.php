<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\PostCategory;
use App\Models\Tag;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->query('q',''));
        $posts = Post::published()
            ->when($q !== '', function($qq) use ($q){
                $qq->where(function($w) use ($q){
                    $w->where('title','like',"%$q%")->orWhere('excerpt','like',"%$q%")->orWhere('content','like',"%$q%");
                });
            })
            ->latest('published_at')->paginate(9)->withQueryString();
        $featured = Post::published()->featured()->latest('published_at')->take(5)->get();
        $categories = PostCategory::orderBy('name')->get();
        $tags = Tag::orderBy('name')->take(20)->get();
        return view('client.blog.index', compact('posts','featured','categories','tags','q'));
    }

    public function category(PostCategory $category)
    {
        $posts = Post::published()->where('category_id', $category->id)->latest('published_at')->paginate(9);
        $featured = Post::published()->featured()->latest('published_at')->take(5)->get();
        $categories = PostCategory::orderBy('name')->get();
        $tags = Tag::orderBy('name')->take(20)->get();
        return view('client.blog.index', compact('posts','featured','categories','tags', 'category'));
    }

    public function tag(Tag $tag)
    {
        $posts = Post::published()->whereHas('tags', fn($q)=>$q->where('tags.id', $tag->id))->latest('published_at')->paginate(9);
        $featured = Post::published()->featured()->latest('published_at')->take(5)->get();
        $categories = PostCategory::orderBy('name')->get();
        $tags = Tag::orderBy('name')->take(20)->get();
        return view('client.blog.index', compact('posts','featured','categories','tags', 'tag'));
    }

    public function show($slug)
    {
        $post = Post::published()->where('slug', $slug)->with(['category','tags'])->firstOrFail();
        $related = Post::published()
            ->where('id','!=',$post->id)
            ->when($post->category_id, fn($q)=>$q->where('category_id', $post->category_id))
            ->latest('published_at')->take(6)->get();
        return view('client.blog.show', compact('post','related'));
    }
}


