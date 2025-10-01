@extends('layouts.admin')

@section('title', 'Novo Comentário')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Novo Comentário</h3>
                    <div>
                        <a href="{{ route('admin.blog-comments.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.blog-comments.store') }}">
                    @csrf

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Comment Information -->
                                <div class="form-group">
                                    <label for="blog_post_id">Post do Blog <span class="text-danger">*</span></label>
                                    <select name="blog_post_id" id="blog_post_id" class="form-control @error('blog_post_id') is-invalid @enderror" required>
                                        <option value="">Selecionar Post</option>
                                        @foreach($blogPosts as $post)
                                            <option value="{{ $post->id }}" {{ old('blog_post_id') == $post->id ? 'selected' : '' }}>
                                                {{ $post->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('blog_post_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="content">Conteúdo do Comentário <span class="text-danger">*</span></label>
                                    <textarea name="content" id="content" rows="6" class="form-control @error('content') is-invalid @enderror" placeholder="Digite o conteúdo do comentário..." required>{{ old('content') }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ old('status', 'pending') == 'pending' ? 'selected' : '' }}>Pendente</option>
                                        <option value="approved" {{ old('status') == 'approved' ? 'selected' : '' }}>Aprovado</option>
                                        <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <!-- Author Information -->
                                <div class="card">
                                    <div class="card-header">
                                        <h5>Informações do Autor</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="author_name">Nome do Autor <span class="text-danger">*</span></label>
                                            <input type="text" name="author_name" id="author_name" class="form-control @error('author_name') is-invalid @enderror" value="{{ old('author_name') }}" placeholder="Nome completo do autor" required>
                                            @error('author_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="author_email">Email do Autor <span class="text-danger">*</span></label>
                                            <input type="email" name="author_email" id="author_email" class="form-control @error('author_email') is-invalid @enderror" value="{{ old('author_email') }}" placeholder="email@exemplo.com" required>
                                            @error('author_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="author_website">Website do Autor</label>
                                            <input type="url" name="author_website" id="author_website" class="form-control @error('author_website') is-invalid @enderror" value="{{ old('author_website') }}" placeholder="https://exemplo.com">
                                            @error('author_website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">URL completa incluindo http:// ou https://</small>
                                        </div>
                                    </div>
                                </div>

                                <!-- Additional Options -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5>Opções Adicionais</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="parent_id">Responder a Comentário</label>
                                            <select name="parent_id" id="parent_id" class="form-control @error('parent_id') is-invalid @enderror">
                                                <option value="">Comentário principal</option>
                                                <!-- This will be populated via JavaScript when a blog post is selected -->
                                            </select>
                                            @error('parent_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">Selecione um post primeiro para ver os comentários disponíveis</small>
                                        </div>

                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle"></i>
                                            <strong>Dica:</strong> Comentários criados pelo admin são automaticamente associados ao IP do servidor.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('admin.blog-comments.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Criar Comentário
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const blogPostSelect = document.getElementById('blog_post_id');
    const parentCommentSelect = document.getElementById('parent_id');

    blogPostSelect.addEventListener('change', function() {
        const postId = this.value;
        
        // Clear parent comment options
        parentCommentSelect.innerHTML = '<option value="">Comentário principal</option>';
        
        if (postId) {
            // Fetch comments for the selected post
            fetch(`/admin/blog-comments/get-comments/${postId}`)
                .then(response => response.json())
                .then(data => {
                    data.forEach(comment => {
                        const option = document.createElement('option');
                        option.value = comment.id;
                        option.textContent = `${comment.author_name}: ${comment.content.substring(0, 50)}...`;
                        parentCommentSelect.appendChild(option);
                    });
                })
                .catch(error => {
                    console.error('Error fetching comments:', error);
                });
        }
    });
});
</script>
@endsection