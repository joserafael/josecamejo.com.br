@extends('layouts.admin')

@section('title', 'Gerenciar Comentários do Blog')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h3 class="card-title">Comentários do Blog</h3>
                    <a href="{{ route('admin.blog-comments.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Novo Comentário
                    </a>
                </div>

                <div class="card-body">
                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row">
                            <div class="col-md-3">
                                <select name="status" class="form-control">
                                    <option value="">Todos os Status</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pendente</option>
                                    <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>Aprovado</option>
                                    <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>Rejeitado</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select name="blog_post_id" class="form-control">
                                    <option value="">Todos os Posts</option>
                                    @foreach($blogPosts as $post)
                                        <option value="{{ $post->id }}" {{ request('blog_post_id') == $post->id ? 'selected' : '' }}>
                                            {{ $post->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <input type="text" name="search" class="form-control" placeholder="Buscar por autor ou conteúdo..." value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-secondary">Filtrar</button>
                            </div>
                        </div>
                    </form>

                    <!-- Bulk Actions -->
                    <form id="bulk-form" method="POST" action="{{ route('admin.blog-comments.bulk-action') }}">
                        @csrf
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <select name="action" class="form-control" required>
                                    <option value="">Selecionar Ação</option>
                                    <option value="approve">Aprovar Selecionados</option>
                                    <option value="reject">Rejeitar Selecionados</option>
                                    <option value="delete">Excluir Selecionados</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-warning" onclick="return confirm('Tem certeza que deseja executar esta ação?')">
                                    Executar Ação
                                </button>
                            </div>
                        </div>

                        <!-- Comments Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th width="30">
                                            <input type="checkbox" id="select-all">
                                        </th>
                                        <th>Autor</th>
                                        <th>Post</th>
                                        <th>Comentário</th>
                                        <th>Status</th>
                                        <th>Data</th>
                                        <th width="150">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($comments as $comment)
                                        <tr>
                                            <td>
                                                <input type="checkbox" name="comment_ids[]" value="{{ $comment->id }}" class="comment-checkbox">
                                            </td>
                                            <td>
                                                <strong>{{ $comment->author_name }}</strong><br>
                                                <small class="text-muted">{{ $comment->author_email }}</small>
                                                @if($comment->parent_id)
                                                    <br><span class="badge badge-info">Resposta</span>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{ route('admin.blog-posts.show', $comment->blogPost) }}" target="_blank">
                                                    {{ Str::limit($comment->blogPost->title, 30) }}
                                                </a>
                                            </td>
                                            <td>
                                                {{ Str::limit($comment->content, 100) }}
                                            </td>
                                            <td>
                                                @if($comment->status === 'approved')
                                                    <span class="badge badge-success">Aprovado</span>
                                                @elseif($comment->status === 'rejected')
                                                    <span class="badge badge-danger">Rejeitado</span>
                                                @else
                                                    <span class="badge badge-warning">Pendente</span>
                                                @endif
                                            </td>
                                            <td>
                                                {{ $comment->created_at->format('d/m/Y H:i') }}
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="{{ route('admin.blog-comments.show', $comment) }}" class="btn btn-sm btn-info">
                                                        <i class="fas fa-eye"></i>
                                                    </a>
                                                    @if($comment->status !== 'approved')
                                                        <a href="{{ route('admin.blog-comments.approve', $comment) }}" class="btn btn-sm btn-success" onclick="return confirm('Aprovar este comentário?')">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                    @endif
                                                    @if($comment->status !== 'rejected')
                                                        <a href="{{ route('admin.blog-comments.reject', $comment) }}" class="btn btn-sm btn-warning" onclick="return confirm('Rejeitar este comentário?')">
                                                            <i class="fas fa-times"></i>
                                                        </a>
                                                    @endif
                                                    <a href="{{ route('admin.blog-comments.edit', $comment) }}" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form method="POST" action="{{ route('admin.blog-comments.destroy', $comment) }}" style="display: inline;">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Excluir este comentário? Esta ação não pode ser desfeita.')">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="7" class="text-center">Nenhum comentário encontrado.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <!-- Pagination -->
                    <div class="d-flex justify-content-center">
                        {{ $comments->appends(request()->query())->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all checkbox functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const commentCheckboxes = document.querySelectorAll('.comment-checkbox');

    selectAllCheckbox.addEventListener('change', function() {
        commentCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });

    // Update select all checkbox when individual checkboxes change
    commentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const checkedCount = document.querySelectorAll('.comment-checkbox:checked').length;
            selectAllCheckbox.checked = checkedCount === commentCheckboxes.length;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < commentCheckboxes.length;
        });
    });
});
</script>
@endsection