@extends('layouts.admin')

@section('title', 'Editar Comentário')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Editar Comentário</h3>
                    <div>
                        <a href="{{ route('admin.blog-comments.show', $blogComment) }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                </div>

                <form method="POST" action="{{ route('admin.blog-comments.update', $blogComment) }}">
                    @csrf
                    @method('PUT')

                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <!-- Comment Information -->
                                <div class="form-group">
                                    <label for="blog_post_id">Post do Blog <span class="text-danger">*</span></label>
                                    <select name="blog_post_id" id="blog_post_id" class="form-control @error('blog_post_id') is-invalid @enderror" required>
                                        <option value="">Selecionar Post</option>
                                        @foreach($blogPosts as $post)
                                            <option value="{{ $post->id }}" {{ old('blog_post_id', $blogComment->blog_post_id) == $post->id ? 'selected' : '' }}>
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
                                    <textarea name="content" id="content" rows="6" class="form-control @error('content') is-invalid @enderror" required>{{ old('content', $blogComment->content) }}</textarea>
                                    @error('content')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="status">Status <span class="text-danger">*</span></label>
                                    <select name="status" id="status" class="form-control @error('status') is-invalid @enderror" required>
                                        <option value="pending" {{ old('status', $blogComment->status) == 'pending' ? 'selected' : '' }}>Pendente</option>
                                        <option value="approved" {{ old('status', $blogComment->status) == 'approved' ? 'selected' : '' }}>Aprovado</option>
                                        <option value="rejected" {{ old('status', $blogComment->status) == 'rejected' ? 'selected' : '' }}>Rejeitado</option>
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
                                            <input type="text" name="author_name" id="author_name" class="form-control @error('author_name') is-invalid @enderror" value="{{ old('author_name', $blogComment->author_name) }}" required>
                                            @error('author_name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="author_email">Email do Autor <span class="text-danger">*</span></label>
                                            <input type="email" name="author_email" id="author_email" class="form-control @error('author_email') is-invalid @enderror" value="{{ old('author_email', $blogComment->author_email) }}" required>
                                            @error('author_email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="form-group">
                                            <label for="author_website">Website do Autor</label>
                                            <input type="url" name="author_website" id="author_website" class="form-control @error('author_website') is-invalid @enderror" value="{{ old('author_website', $blogComment->author_website) }}">
                                            @error('author_website')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <!-- Comment Metadata -->
                                <div class="card mt-3">
                                    <div class="card-header">
                                        <h5>Metadados</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label>IP Address:</label>
                                            <input type="text" class="form-control" value="{{ $blogComment->ip_address }}" readonly>
                                        </div>

                                        <div class="form-group">
                                            <label>Data de Criação:</label>
                                            <input type="text" class="form-control" value="{{ $blogComment->created_at->format('d/m/Y H:i:s') }}" readonly>
                                        </div>

                                        @if($blogComment->approved_at)
                                            <div class="form-group">
                                                <label>Aprovado em:</label>
                                                <input type="text" class="form-control" value="{{ $blogComment->approved_at->format('d/m/Y H:i:s') }}" readonly>
                                            </div>
                                        @endif

                                        @if($blogComment->parent_id)
                                            <div class="form-group">
                                                <label>Resposta ao comentário:</label>
                                                <div class="border p-2 bg-light">
                                                    <strong>{{ $blogComment->parent->author_name }}</strong><br>
                                                    <small>{{ Str::limit($blogComment->parent->content, 100) }}</small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('admin.blog-comments.show', $blogComment) }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save"></i> Salvar Alterações
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection