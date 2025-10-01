<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogPost;
use App\Models\BlogCategory;
use App\Models\BlogSubcategory;
use App\Models\BlogTag;
use App\Models\BlogImage;
use App\Models\BlogVideo;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BlogPostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $query = BlogPost::with(['category', 'subcategory', 'author', 'tags']);

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category_id')) {
            $query->where('blog_category_id', $request->category_id);
        }

        // Filter by language
        if ($request->filled('language')) {
            $query->where('language', $request->language);
        }

        // Search by title or content
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $blogPosts = $query->orderBy('created_at', 'desc')->paginate(15);
        $categories = BlogCategory::active()->orderBy('name')->get();

        return view('admin.blog-posts.index', compact('blogPosts', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $categories = BlogCategory::active()->orderBy('name')->get();
        $subcategories = BlogSubcategory::active()->orderBy('name')->get();
        $tags = BlogTag::active()->orderBy('name')->get();
        $images = BlogImage::active()->orderBy('title')->get();
        $videos = BlogVideo::active()->orderBy('title')->get();

        return view('admin.blog-posts.create', compact('categories', 'subcategories', 'tags', 'images', 'videos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'language' => 'required|in:pt,en,es',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'published_at' => 'nullable|date',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'blog_subcategory_id' => 'nullable|exists:blog_subcategories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:blog_tags,id',
            'images' => 'nullable|array',
            'images.*' => 'exists:blog_images,id',
            'videos' => 'nullable|array',
            'videos.*' => 'exists:blog_videos,id',
        ]);

        $validated['user_id'] = Auth::id();

        // Handle published_at for scheduled posts
        if ($validated['status'] === 'scheduled' && !$validated['published_at']) {
            return back()->withErrors(['published_at' => 'Published date is required for scheduled posts.'])->withInput();
        }

        if ($validated['status'] === 'published' && !$validated['published_at']) {
            $validated['published_at'] = now();
        }

        $post = BlogPost::create($validated);

        // Sync relationships
        if ($request->filled('tags')) {
            $post->tags()->sync($request->tags);
        }

        if ($request->filled('images')) {
            $post->images()->sync($request->images);
        }

        if ($request->filled('videos')) {
            $post->videos()->sync($request->videos);
        }

        return redirect()->route('admin.blog-posts.index')
                        ->with('success', 'Blog post created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogPost $blogPost): View
    {
        $blogPost->load(['category', 'subcategory', 'author', 'tags', 'images', 'videos']);
        
        return view('admin.blog-posts.show', compact('blogPost'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogPost $blogPost): View
    {
        $blogPost->load(['tags', 'images', 'videos']);
        $categories = BlogCategory::active()->orderBy('name')->get();
        $subcategories = BlogSubcategory::active()->orderBy('name')->get();
        $tags = BlogTag::active()->orderBy('name')->get();
        $images = BlogImage::active()->orderBy('title')->get();
        $videos = BlogVideo::active()->orderBy('title')->get();

        return view('admin.blog-posts.edit', compact('blogPost', 'categories', 'subcategories', 'tags', 'images', 'videos'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogPost $blogPost): RedirectResponse
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|string|max:255',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'language' => 'required|in:pt,en,es',
            'status' => 'required|in:draft,published,archived',
            'is_featured' => 'boolean',
            'allow_comments' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'published_at' => 'nullable|date',
            'blog_category_id' => 'required|exists:blog_categories,id',
            'blog_subcategory_id' => 'nullable|exists:blog_subcategories,id',
            'tags' => 'nullable|array',
            'tags.*' => 'exists:blog_tags,id',
            'images' => 'nullable|array',
            'images.*' => 'exists:blog_images,id',
            'videos' => 'nullable|array',
            'videos.*' => 'exists:blog_videos,id',
        ]);

        // Handle published_at for scheduled posts
        if ($validated['status'] === 'scheduled' && !$validated['published_at']) {
            return back()->withErrors(['published_at' => 'Published date is required for scheduled posts.'])->withInput();
        }

        if ($validated['status'] === 'published' && !$validated['published_at'] && $blogPost->status !== 'published') {
            $validated['published_at'] = now();
        }

        $blogPost->update($validated);

        // Sync relationships
        $blogPost->tags()->sync($request->tags ?? []);
        $blogPost->images()->sync($request->images ?? []);
        $blogPost->videos()->sync($request->videos ?? []);

        return redirect()->route('admin.blog-posts.index')
                        ->with('success', 'Blog post updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogPost $blogPost): RedirectResponse
    {
        // Detach all relationships
        $blogPost->tags()->detach();
        $blogPost->images()->detach();
        $blogPost->videos()->detach();

        $blogPost->delete();

        return redirect()->route('admin.blog-posts.index')
                        ->with('success', 'Blog post deleted successfully.');
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(BlogPost $blogPost): RedirectResponse
    {
        $blogPost->update(['is_featured' => !$blogPost->is_featured]);

        $status = $blogPost->is_featured ? 'featured' : 'unfeatured';
        return back()->with('success', "Blog post {$status} successfully.");
    }

    /**
     * Duplicate a blog post
     */
    public function duplicate(BlogPost $blogPost): RedirectResponse
    {
        $newPost = $blogPost->replicate();
        $newPost->title = $newPost->title . ' (Copy)';
        $newPost->slug = null; // Will be auto-generated
        $newPost->status = 'draft';
        $newPost->published_at = null;
        $newPost->views_count = 0;
        $newPost->user_id = Auth::id();
        $newPost->save();

        // Copy relationships
        $newPost->tags()->sync($blogPost->tags->pluck('id'));
        $newPost->images()->sync($blogPost->images->pluck('id'));
        $newPost->videos()->sync($blogPost->videos->pluck('id'));

        return redirect()->route('admin.blog-posts.edit', $newPost)
                        ->with('success', 'Blog post duplicated successfully.');
    }
}
