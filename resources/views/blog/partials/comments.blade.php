@foreach($comments as $comment)
    <div class="comment-item" id="comment-{{ $comment->id }}">
        <div class="comment-card">
            <div class="comment-header">
                <!-- Avatar -->
                <div class="comment-avatar">
                    <img src="{{ $comment->gravatar }}" alt="{{ $comment->author_name }}" class="avatar-image">
                </div>
                
                <!-- Author Info -->
                <div class="comment-author-info">
                    <div class="comment-author">
                        @if($comment->author_website)
                            <a href="{{ $comment->author_website }}" target="_blank" rel="nofollow" class="author-link">
                                {{ $comment->author_name }}
                            </a>
                        @else
                            <span class="author-name">{{ $comment->author_name }}</span>
                        @endif
                    </div>
                    <div class="comment-meta">
                        <span class="comment-time">
                            <i class="fas fa-clock"></i> 
                            {{ $comment->created_at->diffForHumans() }}
                        </span>
                        @if($comment->isReply())
                            <span class="comment-reply-info">
                                <i class="fas fa-reply"></i> 
                                {{ __('blog.reply_to_user') }} {{ $comment->parent->author_name }}
                            </span>
                        @endif
                    </div>
                </div>
                
                <!-- Reply Button -->
                <div class="comment-actions">
                    <button type="button" class="comment-reply-btn" onclick="replyToComment({{ $comment->id }}, '{{ addslashes($comment->author_name) }}')">
                        <i class="fas fa-reply"></i>
                        <span>{{ __('blog.reply') }}</span>
                    </button>
                </div>
            </div>
            
            <!-- Comment Content -->
            <div class="comment-content">
                {!! nl2br(e($comment->content)) !!}
            </div>
        </div>
        
        <!-- Replies -->
        @if($comment->replies->count() > 0)
            <div class="comment-replies">
                @include('blog.partials.comments', ['comments' => $comment->replies])
            </div>
        @endif
    </div>
@endforeach