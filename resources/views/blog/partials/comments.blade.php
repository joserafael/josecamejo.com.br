@foreach($comments as $comment)
    <div class="comment mb-4" id="comment-{{ $comment->id }}">
        <div class="card">
            <div class="card-body">
                <div class="d-flex">
                    <!-- Avatar -->
                    <div class="me-3">
                        <img src="{{ $comment->gravatar }}" alt="{{ $comment->author_name }}" class="rounded-circle" width="50" height="50">
                    </div>
                    
                    <!-- Comment Content -->
                    <div class="flex-grow-1">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <h6 class="mb-1">
                                    @if($comment->author_website)
                                        <a href="{{ $comment->author_website }}" target="_blank" rel="nofollow" class="text-decoration-none">
                                            {{ $comment->author_name }}
                                        </a>
                                    @else
                                        {{ $comment->author_name }}
                                    @endif
                                </h6>
                                <small class="text-muted">
                                    <i class="fas fa-clock"></i> 
                                    {{ $comment->created_at->diffForHumans() }}
                                    @if($comment->isReply())
                                        | <i class="fas fa-reply"></i> 
                                        Resposta para {{ $comment->parent->author_name }}
                                    @endif
                                </small>
                            </div>
                            
                            <!-- Reply Button -->
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="replyToComment({{ $comment->id }}, '{{ addslashes($comment->author_name) }}')">
                                <i class="fas fa-reply"></i> Responder
                            </button>
                        </div>
                        
                        <!-- Comment Text -->
                        <div class="comment-content">
                            {!! nl2br(e($comment->content)) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Replies -->
        @if($comment->replies->count() > 0)
            <div class="replies ms-4 mt-3">
                @include('blog.partials.comments', ['comments' => $comment->replies])
            </div>
        @endif
    </div>
@endforeach