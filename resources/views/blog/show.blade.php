@extends('layouts.app')

@section('title', $post->title)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-lg-8">
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
                            <form id="comment-form" method="POST" action="{{ route('comments.store') }}">
                                @csrf
                                <input type="hidden" name="blog_post_id" value="{{ $post->id }}">
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

        <div class="col-lg-4">
            <!-- Related Posts -->
            @if($relatedPosts->count() > 0)
                <div class="card mb-4">
                    <div class="card-header">
                        <h5><i class="fas fa-newspaper"></i> Posts Relacionados</h5>
                    </div>
                    <div class="card-body">
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
            <div class="card mb-4">
                <div class="card-header">
                    <h5><i class="fas fa-share-alt"></i> Compartilhar</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}" target="_blank" class="btn btn-primary btn-sm">
                            <i class="fab fa-facebook-f"></i> Facebook
                        </a>
                        <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($post->title) }}" target="_blank" class="btn btn-info btn-sm">
                            <i class="fab fa-twitter"></i> Twitter
                        </a>
                        <a href="https://www.linkedin.com/sharing/share-offsite/?url={{ urlencode(request()->url()) }}" target="_blank" class="btn btn-primary btn-sm">
                            <i class="fab fa-linkedin-in"></i> LinkedIn
                        </a>
                        <a href="https://wa.me/?text={{ urlencode($post->title . ' - ' . request()->url()) }}" target="_blank" class="btn btn-success btn-sm">
                            <i class="fab fa-whatsapp"></i> WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <!-- Back to Blog -->
            <div class="card">
                <div class="card-body text-center">
                    <a href="{{ route('blog.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-arrow-left"></i> Voltar ao Blog
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Comment form submission
    const commentForm = document.getElementById('comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Enviando...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show success message
                    showAlert('success', data.message);
                    
                    // Reset form
                    this.reset();
                    cancelReply();
                    
                    // Reload comments if needed
                    if (data.reload_comments) {
                        loadComments();
                    }
                } else {
                    // Show error message
                    showAlert('danger', data.message || 'Erro ao enviar comentário.');
                    
                    // Show validation errors
                    if (data.errors) {
                        Object.keys(data.errors).forEach(field => {
                            const input = document.querySelector(`[name="${field}"]`);
                            if (input) {
                                input.classList.add('is-invalid');
                                const feedback = input.parentNode.querySelector('.invalid-feedback');
                                if (feedback) {
                                    feedback.textContent = data.errors[field][0];
                                }
                            }
                        });
                    }
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Erro ao enviar comentário. Tente novamente.');
            })
            .finally(() => {
                // Re-enable submit button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
});

function replyToComment(commentId, authorName) {
    document.getElementById('parent_id').value = commentId;
    document.getElementById('reply-to-name').textContent = authorName;
    document.getElementById('reply-info').style.display = 'block';
    
    // Scroll to comment form
    document.getElementById('comment-form').scrollIntoView({ behavior: 'smooth' });
    
    // Focus on content textarea
    document.getElementById('content').focus();
}

function cancelReply() {
    document.getElementById('parent_id').value = '';
    document.getElementById('reply-info').style.display = 'none';
}

function loadComments() {
    fetch(`{{ route('comments.get', $post->id) }}`)
        .then(response => response.text())
        .then(html => {
            document.getElementById('comments-list').innerHTML = html;
        })
        .catch(error => {
            console.error('Error loading comments:', error);
        });
}

function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
    alertDiv.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    // Insert alert before the comment form
    const commentForm = document.getElementById('comment-form');
    commentForm.parentNode.insertBefore(alertDiv, commentForm);
    
    // Auto-remove alert after 5 seconds
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
</script>
@endsection