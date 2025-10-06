@extends('layouts.blog')

@section('title', $post->title . ' - Blog')

@section('content')
<div class="blog-container">
    <div class="blog-grid">
        <div class="blog-content">
            <!-- Blog Post -->
            <article class="mb-5">
                <header class="mb-4">
                    <h1 class="mb-3">{{ $post->title }}</h1>
                    
                    <div class="text-muted mb-3">
                        <i class="fas fa-calendar"></i> {{ $post->published_at->format('d/m/Y H:i') }}
                        @if($post->category)
                            | <i class="fas fa-folder"></i> 
                            <a href="{{ route('blog.category', $post->category->slug) }}" class="text-decoration-none">
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
                                <a href="{{ route('blog.tag', $tag->slug) }}" class="badge badge-secondary text-decoration-none me-1">
                                    {{ $tag->name }}
                                </a>
                            @endforeach
                        </div>
                    @endif
                </header>

                @if($post->featured_image)
                    <div class="mb-4">
                        <img src="{{ asset('storage/' . $post->featured_image) }}" class="img-fluid rounded" alt="{{ $post->title }}">
                    </div>
                @endif

                <div class="content">
                    {!! $post->content !!}
                </div>
            </article>

            <!-- Comments Section -->
            @if($post->allow_comments)
                <section id="comments">
                    <h3 class="comments-section-title">
                        <i class="fas fa-comments"></i> 
                        Comentários 
                        <span class="comments-count">{{ $post->approvedComments->count() }}</span>
                    </h3>

                    <!-- Modern Comment Form -->
                    <div class="comment-form-container">
                        <div class="comment-form-header">
                            <h5 class="comment-form-title">
                                <i class="fas fa-plus"></i> 
                                Deixe seu comentário
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
                                        Respondendo a: <span class="reply-notification-name" id="reply-to-name"></span>
                                    </span>
                                    <button type="button" class="cancel-reply-btn" onclick="cancelReply()">
                                        <i class="fas fa-times"></i> Cancelar
                                    </button>
                                </div>

                                <!-- Form Fields -->
                                <div class="comment-form-row">
                                    <div class="comment-form-group half-width with-icon">
                                        <label for="author_name" class="comment-form-label">
                                            Nome <span class="required">*</span>
                                        </label>
                                        <i class="comment-form-icon fas fa-user"></i>
                                        <input type="text" 
                                               name="author_name" 
                                               id="author_name" 
                                               class="comment-form-input @error('author_name') is-invalid @enderror" 
                                               value="{{ old('author_name') }}" 
                                               placeholder="Seu nome completo"
                                               required>
                                        @error('author_name')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <div class="comment-form-group half-width with-icon">
                                        <label for="author_email" class="comment-form-label">
                                            Email <span class="required">*</span>
                                        </label>
                                        <i class="comment-form-icon fas fa-envelope"></i>
                                        <input type="email" 
                                               name="author_email" 
                                               id="author_email" 
                                               class="comment-form-input @error('author_email') is-invalid @enderror" 
                                               value="{{ old('author_email') }}" 
                                               placeholder="seu@email.com"
                                               required>
                                        @error('author_email')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                        <div class="form-help-text">
                                            <i class="fas fa-shield-alt"></i>
                                            Seu email não será publicado
                                        </div>
                                    </div>
                                </div>

                                <div class="comment-form-group with-icon">
                                    <label for="author_website" class="comment-form-label">Website</label>
                                    <i class="comment-form-icon fas fa-globe"></i>
                                    <input type="url" 
                                           name="author_website" 
                                           id="author_website" 
                                           class="comment-form-input @error('author_website') is-invalid @enderror" 
                                           value="{{ old('author_website') }}" 
                                           placeholder="https://seusite.com">
                                    @error('author_website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="comment-form-group">
                                    <label for="content" class="comment-form-label">
                                        Comentário <span class="required">*</span>
                                    </label>
                                    <textarea name="content" 
                                              id="content" 
                                              class="comment-form-textarea @error('content') is-invalid @enderror" 
                                              placeholder="Compartilhe seus pensamentos sobre este post..."
                                              required>{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- CAPTCHA Field -->
                                <div class="comment-form-group">
                                    <label for="captcha_answer" class="comment-form-label">
                                        Verificação <span class="required">*</span>
                                    </label>
                                    <div class="captcha-container">
                                        <span class="captcha-question" id="captchaQuestion">Carregando...</span>
                                        <input type="number" 
                                               id="captcha_answer" 
                                               name="captcha_answer" 
                                               class="comment-form-input @error('captcha_answer') is-invalid @enderror" 
                                               placeholder="Sua resposta"
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
                                        Resolva a operação matemática para verificar que você é humano
                                    </div>
                                </div>

                                <div class="comment-form-actions">
                                    <div class="comment-form-info">
                                        <i class="fas fa-info-circle"></i> 
                                        Seu comentário será revisado antes da publicação
                                    </div>
                                    <button type="submit" class="comment-submit-btn">
                                        <span class="btn-text">
                                            <i class="fas fa-paper-plane"></i> 
                                            Enviar Comentário
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
                            <p class="text-muted">Seja o primeiro a comentar!</p>
                        </div>
                    @endif
                </section>
            @else
                <div class="alert alert-info mt-5">
                    <i class="fas fa-info-circle"></i> 
                    Os comentários estão desabilitados para este post.
                </div>
            @endif
        </div>

        <div class="blog-sidebar">
            <!-- Related Posts -->
            @if($relatedPosts->count() > 0)
                <div class="sidebar-section">
                    <h3 class="sidebar-title">
                        <i class="fas fa-newspaper"></i> Posts Relacionados
                    </h3>
                    <div class="sidebar-content">
                        @foreach($relatedPosts as $relatedPost)
                            <div class="mb-3">
                                @if($relatedPost->featured_image)
                                    <img src="{{ asset('storage/' . $relatedPost->featured_image) }}" class="img-fluid rounded mb-2" alt="{{ $relatedPost->title }}" style="height: 100px; width: 100%; object-fit: cover;">
                                @endif
                                <h6>
                                    <a href="{{ route('blog.show', $relatedPost->slug) }}" class="text-decoration-none">
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
                    <i class="fas fa-share-alt"></i> Compartilhar
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

            <!-- Back to Blog -->
            <div class="sidebar-section">
                <div class="sidebar-content text-center">
                    <a href="{{ route('blog.index') }}" class="btn-back-to-blog">
                        <i class="fas fa-arrow-left"></i> Voltar ao Blog
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection