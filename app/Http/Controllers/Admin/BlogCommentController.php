<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogComment;
use App\Models\BlogPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogCommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = BlogComment::with(['blogPost', 'parent', 'approvedBy']);

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        // Filter by blog post
        if ($request->has('blog_post_id') && $request->blog_post_id !== '') {
            $query->where('blog_post_id', $request->blog_post_id);
        }

        // Search by author name or content
        if ($request->has('search') && $request->search !== '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('author_name', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%")
                  ->orWhere('author_email', 'like', "%{$search}%");
            });
        }

        $comments = $query->orderBy('created_at', 'desc')->paginate(20);
        $blogPosts = BlogPost::select('id', 'title')->orderBy('title')->get();

        return view('admin.blog-comments.index', compact('comments', 'blogPosts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $blogPosts = BlogPost::select('id', 'title')->orderBy('title')->get();
        return view('admin.blog-comments.create', compact('blogPosts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'blog_post_id' => 'required|exists:blog_posts,id',
            'parent_id' => 'nullable|exists:blog_comments,id',
            'author_name' => 'required|string|max:255',
            'author_email' => 'required|email|max:255',
            'author_website' => 'nullable|url|max:255',
            'content' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $validated['ip_address'] = $request->ip();
        $validated['user_agent'] = $request->userAgent();

        if ($validated['status'] === 'approved') {
            $validated['approved_at'] = now();
            $validated['approved_by'] = Auth::id();
        }

        BlogComment::create($validated);

        return redirect()->route('admin.blog-comments.index')
                        ->with('success', 'Comment created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(BlogComment $blogComment)
    {
        $blogComment->load(['blogPost', 'parent', 'replies', 'approvedBy']);
        return view('admin.blog-comments.show', compact('blogComment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BlogComment $blogComment)
    {
        $blogPosts = BlogPost::select('id', 'title')->orderBy('title')->get();
        return view('admin.blog-comments.edit', compact('blogComment', 'blogPosts'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BlogComment $blogComment)
    {
        $validated = $request->validate([
            'blog_post_id' => 'required|exists:blog_posts,id',
            'parent_id' => 'nullable|exists:blog_comments,id',
            'author_name' => 'required|string|max:255',
            'author_email' => 'required|email|max:255',
            'author_website' => 'nullable|url|max:255',
            'content' => 'required|string',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        // Handle status change
        if ($validated['status'] !== $blogComment->status) {
            if ($validated['status'] === 'approved') {
                $validated['approved_at'] = now();
                $validated['approved_by'] = Auth::id();
            } else {
                $validated['approved_at'] = null;
                $validated['approved_by'] = null;
            }
        }

        $blogComment->update($validated);

        return redirect()->route('admin.blog-comments.index')
                        ->with('success', 'Comment updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BlogComment $blogComment)
    {
        // Delete all replies first
        $blogComment->replies()->delete();
        
        // Delete the comment
        $blogComment->delete();

        return redirect()->route('admin.blog-comments.index')
                        ->with('success', 'Comment and its replies deleted successfully.');
    }

    /**
     * Approve a comment
     */
    public function approve(BlogComment $blogComment)
    {
        $blogComment->approve(Auth::id());

        return back()->with('success', 'Comment approved successfully.');
    }

    /**
     * Reject a comment
     */
    public function reject(BlogComment $blogComment)
    {
        $blogComment->reject();

        return back()->with('success', 'Comment rejected successfully.');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:approve,reject,delete',
            'comment_ids' => 'required|array',
            'comment_ids.*' => 'exists:blog_comments,id',
        ]);

        $comments = BlogComment::whereIn('id', $request->comment_ids);

        switch ($request->action) {
            case 'approve':
                $comments->update([
                    'status' => 'approved',
                    'approved_at' => now(),
                    'approved_by' => Auth::id(),
                ]);
                $message = 'Comments approved successfully.';
                break;

            case 'reject':
                $comments->update([
                    'status' => 'rejected',
                    'approved_at' => null,
                    'approved_by' => null,
                ]);
                $message = 'Comments rejected successfully.';
                break;

            case 'delete':
                // Delete replies first
                BlogComment::whereIn('parent_id', $request->comment_ids)->delete();
                // Delete comments
                $comments->delete();
                $message = 'Comments deleted successfully.';
                break;
        }

        return back()->with('success', $message);
    }

    /**
     * Get comments for a specific blog post (AJAX)
     */
    public function getCommentsByPost(BlogPost $post)
    {
        $comments = $post->comments()
            ->with('parent')
            ->select('id', 'author_name', 'content', 'parent_id')
            ->get()
            ->map(function ($comment) {
                return [
                    'id' => $comment->id,
                    'author_name' => $comment->author_name,
                    'content' => Str::limit($comment->content, 100),
                    'parent_id' => $comment->parent_id,
                ];
            });

        return response()->json($comments);
    }
}
