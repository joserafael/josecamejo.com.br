<?php

namespace App\Http\Controllers;

use App\Models\BlogComment;
use App\Models\BlogPost;
use App\Http\Requests\StoreCommentRequest;
use App\Services\CaptchaService;
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
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Comments are not allowed for this post.'], 403);
            }
            abort(403, 'Comments are not allowed for this post.');
        }
        
        // Rate limiting check
        $key = 'comment-submission:' . request()->ip();
        if (RateLimiter::tooManyAttempts($key, 3)) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Too many comment attempts. Please try again later.'], 429);
            }
            abort(429, 'Too many comment attempts. Please try again later.');
        }
        
        // Validation is handled by StoreCommentRequest
        $validated = $request->validated();
        
        // Additional validation for parent comment belonging to the same post
        if (!empty($validated['parent_id'])) {
            $parentComment = BlogComment::find($validated['parent_id']);
            if (!$parentComment || $parentComment->blog_post_id != $blogPost->id) {
                if ($request->expectsJson()) {
                    return response()->json(['success' => false, 'message' => 'Invalid parent comment.'], 422);
                }
                return back()->withErrors(['parent_id' => 'Invalid parent comment.'])->withInput();
            }
        }
        
        // Hit rate limiter
        RateLimiter::hit($key, 300); // 5 minutes

        // Check if the author has previously approved comments
        $hasApprovedComments = BlogComment::where('author_email', $validated['author_email'])
            ->where('status', 'approved')
            ->exists();
            
        $status = $hasApprovedComments ? 'approved' : 'pending';

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
            'status' => $status,
        ]);

        // Clear the CAPTCHA from session after successful submission
        session()->forget('captcha_result');

        $message = $status === 'approved' 
            ? 'Seu comentário foi publicado com sucesso!' 
            : 'Seu comentário foi enviado e está aguardando aprovação.';

        if ($request->expectsJson()) {
            if ($status === 'approved') {
                session()->flash('success', $message);
            }
            
            return response()->json([
                'success' => true,
                'message' => $message,
                'reload_comments' => $status === 'approved'
            ]);
        }

        return back()->with('success', $message);
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
