@extends('layouts.blog')

@section('title', $post->title . ' - Blog')

@section('content')
<div class="blog-container">
    <div class="blog-grid">
        <div class="blog-content">
            <!-- Blog Post -->
            <article class="mb-5">
                <header class="mb-4">
                    <h1 class="mb-3 blog-post-title-main">{{ $post->title }}</h1>
                    
                    <div class="text-muted mb-3">
                        <i class="fas fa-calendar"></i> {{ $post->published_at->format('d/m/Y H:i') }}
                        @if($post->category)
                            | <i class="fas fa-folder"></i> 
                            <a href="{{ route('blog.category', ['locale' => app()->getLocale(), 'slug' => $post->category->slug]) }}" class="text-decoration-none">
                                {{ $post->category->name }}
                            </a>
                        @endif
                        @if($post->author)
                            | <i class="fas fa-user"></i> {{ $post->author->name }}
                        @endif
                    </div>

                    @if($post->tags->count() > 0)
                        <div class="mb-3">
                            @foreach($post->tags as $tag)
                                <a href="{{ route('blog.tag', ['locale' => app()->getLocale(), 'slug' => $tag->slug]) }}" class="badge badge-secondary text-decoration-none me-1">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </header>

                @if($post->featured_image)
                    <div class="mb-4">
                        <img src="{{ asset($post->featured_image) }}" class="img-fluid rounded" alt="{{ $post->title }}">
                    </div>
                @endif

                <div class="content p-6">
                    {!! Str::markdown($post->content) !!}
                </div>
            </article>

            <!-- Comments Section -->
            @if($post->allow_comments)
                <section id="comments">
                    <h3 class="comments-section-title">
                        <i class="fas fa-comments"></i> 
                        {{ __('blog.comments') }} 
                        <span class="comments-count">{{ $post->approvedComments->count() }}</span>
                    </h3>

                    <!-- Modern Comment Form -->
                    <div class="comment-form-container">
                        @if(session('success'))
                            <div class="blog-message blog-message-success">
                                <i class="fas fa-check-circle"></i>
                                {{ session('success') }}
                                <button type="button" class="blog-message-close" onclick="this.parentElement.remove()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        @endif
                        <div class="comment-form-header">
                            <h5 class="comment-form-title">
                                <i class="fas fa-plus"></i> 
                                {{ __('blog.leave_comment') }}
                            </h5>
                        </div>
                        <div class="comment-form-body">
                            <form id="comment-form" method="POST" action="{{ route('comments.store', $post) }}">
                                @csrf
                                <input type="hidden" name="parent_id" id="parent_id" value="">

                                <!-- Reply Notification -->
                                <div id="reply-info" class="reply-notification">
                                    <i class="fas fa-reply"></i> 
                                    <span class="reply-notification-text">
                                        {{ __('blog.replying_to') }} <span class="reply-notification-name" id="reply-to-name"></span>
                                    </span>
                                    <button type="button" class="cancel-reply-btn" onclick="cancelReply()">
                                        <i class="fas fa-times"></i> {{ __('blog.cancel') }}
                                    </button>
                                </div>

                                <!-- Form Fields -->
                                <div class="comment-form-row">
                                    <div class="comment-form-group half-width with-icon">
                                        <label for="author_name" class="comment-form-label">
                                            {{ __('blog.name') }} <span class="required">*</span>
                                        </label>
                                    
                                        <input type="text" 
                                               name="author_name" 
                                               id="author_name" 
                                               class="comment-form-input @error('author_name') is-invalid @enderror" 
                                               value="{{ old('author_name') }}" 
                                               placeholder="{{ __('blog.name_placeholder') }}"
                                               required>
                                        @error('author_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="comment-form-group half-width with-icon">
                                        <label for="author_email" class="comment-form-label">
                                            {{ __('blog.email') }} <span class="required">*</span>
                                        </label>
                                        <input type="email" 
                                               name="author_email" 
                                               id="author_email" 
                                               class="comment-form-input @error('author_email') is-invalid @enderror" 
                                               value="{{ old('author_email') }}" 
                                               placeholder="{{ __('blog.email_placeholder') }}"
                                               required>
                                        @error('author_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-help-text">
                                            <i class="fas fa-shield-alt"></i>
                                            {{ __('blog.email_privacy') }}
                                        </div>
                                    </div>
                                </div>

                                <div class="comment-form-group with-icon">
                                    <label for="author_website" class="comment-form-label">{{ __('blog.website') }}</label>
                                  
                                    <input type="url" 
                                           name="author_website" 
                                           id="author_website" 
                                           class="comment-form-input @error('author_website') is-invalid @enderror" 
                                           value="{{ old('author_website') }}" 
                                           placeholder="{{ __('blog.website_placeholder') }}">
                                    @error('author_website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="comment-form-group">
                                    <label for="content" class="comment-form-label">
                                        {{ __('blog.comment') }} <span class="required">*</span>
                                    </label>
                                    <textarea name="content" 
                                              id="content" 
                                              class="comment-form-textarea @error('content') is-invalid @enderror" 
                                              placeholder="{{ __('blog.comment_placeholder') }}"
                                              required>{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- CAPTCHA Field -->
                                <div class="comment-form-group">
                                    <label for="captcha_answer" class="comment-form-label">
                                        {{ __('blog.verification') }} <span class="required">*</span>
                                    </label>
                                    <div class="captcha-container">
                                        <span class="captcha-question" id="captchaQuestion">Carregando...</span>
                                        <input type="number" 
                                               id="captcha_answer" 
                                               name="captcha_answer" 
                                               class="comment-form-input @error('captcha_answer') is-invalid @enderror" 
                                               placeholder="{{ __('blog.captcha_placeholder') }}"
                                               required>
                                        <button type="button" class="captcha-refresh" id="refreshCaptcha" title="Gerar nova pergunta">
                                            <i class="fas fa-sync-alt"></i>
                                        </button>
                                    </div>
                                    @error('captcha_answer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-help-text">
                                        <i class="fas fa-shield-alt"></i>
                                        {{ __('blog.captcha_help') }}
                                    </div>
                                </div>

                                <div class="comment-form-actions">
                                    <div class="comment-form-info">
                                        <i class="fas fa-info-circle"></i> 
                                        {{ __('blog.comment_review_notice') }}
                                    </div>
                                    <button type="submit" class="comment-submit-btn">
                                        <span class="btn-text">
                                            <i class="fas fa-paper-plane"></i> 
                                            {{ __('blog.submit_comment') }}
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Comments List -->
                    <div id="comments-list">
                        @include('blog.partials.comments', ['comments' => $post->topLevelComments])
                    </div>

                    @if($post->approvedComments->count() == 0)
                        <div class="text-center py-4">
                            <i class="fas fa-comment fa-2x text-muted mb-3"></i>
                            <p class="text-muted">{{ __('blog.be_first_comment') }}</p>
                        </div>
                    @endif
                </section>
            @else
                <div class="alert alert-info mt-5">
                    <i class="fas fa-info-circle"></i> 
                    {{ __('blog.comments_disabled') }}
                </div>
            @endif
        </div>

        <div class="blog-sidebar">
            <!-- Related Posts -->
            @if($relatedPosts->count() > 0)
                <div class="sidebar-section">
                    <h3 class="sidebar-title">
                        <i class="fas fa-newspaper"></i> {{ __('blog.related_posts') }}
                    </h3>
                    <div class="sidebar-content">
                        @foreach($relatedPosts as $relatedPost)
                            <div class="mb-3">
                                @if($relatedPost->featured_image)
                                    <img src="{{ asset('storage/' . $relatedPost->featured_image) }}" class="img-fluid rounded mb-2" alt="{{ $relatedPost->title }}" style="height: 100px; width: 100%; object-fit: cover;">
                                @endif
                                <h6>
                                    <a href="{{ route('blog.show', ['locale' => app()->getLocale(), 'slug' => $relatedPost->slug]) }}" class="text-decoration-none">
                                        {{ Str::limit($relatedPost->title, 60) }}
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar"></i> {{ $relatedPost->published_at->format('d/m/Y') }}
                                </small>
                            </div>
                            @if(!$loop->last)<hr>@endif
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Share -->
            <div class="sidebar-section">
                <h3 class="sidebar-title">
                    <i class="fas fa-share-alt"></i> {{ __('blog.share') }}
                </h3>
                <div class="sidebar-content">
                    <div class="share-buttons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="share-btn facebook">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" target="_blank" class="share-btn twitter">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" target="_blank" class="share-btn linkedin">
                            <i class="fab fa-linkedin-in"></i> LinkedIn
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($post->title . ' - ' . request()->url()) }}" target="_blank" class="share-btn whatsapp">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            @include('blog.partials.sidebar')

            <!-- Back to Blog -->
            <div class="sidebar-section">
                <div class="sidebar-content text-center">
                    <a href="{{ route('blog.index', ['locale' => app()->getLocale()]) }}" class="btn-back-to-blog">
                        <i class="fas fa-arrow-left"></i> {{ __('blog.back_to_blog') }}
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/markdown.css') }}">
@endpush