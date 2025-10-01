<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogTag;

class BlogController extends Controller
{
    /**
     * Display a listing of published blog posts
     */
    public function index(Request $request)
    {
        $query = BlogPost::with(['category', 'subcategory', 'author', 'tags'])
            ->where('status', 'published')
            ->where('published_at', '<=', now());

        // Filter by category
        if ($request->has('category') && $request->category !== '') {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        // Filter by tag
        if ($request->has('tag') && $request->tag !== '') {
            $query->whereHas('tags', function ($q) use ($request) {
                $q->where('slug', $request->tag);
            });
        }

        // Search functionality
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        $posts = $query->orderBy('published_at', 'desc')->paginate(12);
        
        // Get categories with post counts
        $categories = BlogCategory::active()
            ->withCount(['posts' => function ($query) {
                $query->where('status', 'published')
                      ->where('published_at', '<=', now());
            }])
            ->having('posts_count', '>', 0)
            ->orderBy('name')
            ->get();
        
        // Get tags
        $tags = BlogTag::active()
            ->whereHas('posts', function ($query) {
                $query->where('status', 'published')
                      ->where('published_at', '<=', now());
            })
            ->orderBy('name')
            ->get();
        
        // Get recent posts
        $recentPosts = BlogPost::with(['category'])
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->orderBy('published_at', 'desc')
            ->limit(5)
            ->get();

        return view('blog.index', compact('posts', 'categories', 'tags', 'recentPosts'));
    }

    /**
     * Display the specified blog post
     */
    public function show($slug)
    {
        $post = BlogPost::with([
                'category', 
                'subcategory', 
                'author', 
                'tags', 
                'images', 
                'videos',
                'topLevelComments.replies.replies',
                'approvedComments'
            ])
            ->where('slug', $slug)
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->firstOrFail();

        // Get related posts
        $relatedPosts = BlogPost::with(['category', 'author'])
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->where('id', '!=', $post->id)
            ->where(function ($query) use ($post) {
                $query->where('blog_category_id', $post->blog_category_id)
                      ->orWhereHas('tags', function ($q) use ($post) {
                          $q->whereIn('blog_tags.id', $post->tags->pluck('id'));
                      });
            })
            ->orderBy('published_at', 'desc')
            ->limit(3)
            ->get();

        return view('blog.show', compact('post', 'relatedPosts'));
    }

    /**
     * Display posts by category
     */
    public function category($slug)
    {
        $category = BlogCategory::where('slug', $slug)->firstOrFail();
        
        $posts = BlogPost::with(['category', 'subcategory', 'author', 'tags'])
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->where('blog_category_id', $category->id)
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog.category', compact('posts', 'category'));
    }

    /**
     * Display posts by tag
     */
    public function tag($slug)
    {
        $tag = BlogTag::where('slug', $slug)->firstOrFail();
        
        $posts = BlogPost::with(['category', 'subcategory', 'author', 'tags'])
            ->where('status', 'published')
            ->where('published_at', '<=', now())
            ->whereHas('tags', function ($q) use ($tag) {
                $q->where('blog_tags.id', $tag->id);
            })
            ->orderBy('published_at', 'desc')
            ->paginate(12);

        return view('blog.tag', compact('posts', 'tag'));
    }
}
