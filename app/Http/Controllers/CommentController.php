<?php

namespace App\Http\Controllers;

use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Http\Requests\StoreCommentRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;

class CommentController extends Controller
{
    /**
     * Store a new comment
     */
    public function store(StoreCommentRequest $request, BlogPost $blogPost)
    {
        // Check if the blog post allows comments and is published
        if (!$blogPost->allow_comments || $blogPost->status !== 'published') {
            abort(403, 'Comments are not allowed for this post.');
        }
        
        // Rate limiting check
        $key = 'comment-submission:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            abort(429, 'Too many comment attempts. Please try again later.');
        }
        
        // Validation is handled by StoreCommentRequest
        $validated = $request->validated();
        
        // Additional validation for parent comment belonging to the same post
        if (!empty($validated['parent_id'])) {
            $parentComment = BlogComment::find($validated['parent_id']);
            if (!$parentComment || $parentComment->blog_post_id != $blogPost->id) {
                return back()->withErrors(['parent_id' => 'Invalid parent comment.'])->withInput();
            }
        }
        
        // Hit rate limiter
        RateLimiter::hit($key, 300); // 5 minutes

        // Create the comment
        $comment = BlogComment::create([
            'blog_post_id' => $blogPost->id,
            'parent_id' => $validated['parent_id'] ?? null,
            'author_name' => $validated['author_name'],
            'author_email' => $validated['author_email'],
            'author_website' => $validated['author_website'] ?? null,
            'content' => $validated['content'],
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'status' => 'pending', // Comments start as pending
        ]);

        return back()->with('success', 'Your comment has been submitted and is awaiting approval.');
    }

    /**
     * Get comments for a blog post (AJAX)
     */
    public function getComments(BlogPost $blogPost)
    {
        if (!$blogPost->allow_comments) {
            return response()->json(['error' => 'Comments are not allowed for this post.'], 403);
        }

        $comments = $blogPost->topLevelComments()
            ->with(['replies' => function ($query) {
                $query->approved()->orderBy('created_at', 'asc');
            }])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'comments' => $comments,
            'total' => $comments->count(),
        ]);
    }

    /**
     * Load more replies for a comment (AJAX)
     */
    public function getReplies(BlogComment $comment)
    {
        $replies = $comment->replies()
            ->approved()
            ->orderBy('created_at', 'asc')
            ->get();

        return response()->json([
            'replies' => $replies,
            'total' => $replies->count(),
        ]);
    }
}
