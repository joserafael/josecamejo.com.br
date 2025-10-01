@extends('layouts.admin')

@section('title', 'Gerenciar Comentários do Blog')

@section('content')
<div class="admin-comments-page">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="page-title-section">
                <h1 class="page-title">
                    <i class="fas fa-comments text-blue-600"></i>
                    Comentários do Blog
                </h1>
                <p class="page-description">Gerencie todos os comentários do seu blog</p>
            </div>
            <div class="page-actions">
                <a href="{{ route('admin.blog-comments.create') }}" class="btn-primary">
                    <i class="fas fa-plus"></i>
                    Novo Comentário
                </a>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="stats-grid">
        <div class="stat-card stat-card-pending">
            <div class="stat-icon">
                <i class="fas fa-clock"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $comments->where('status', 'pending')->count() }}</div>
                <div class="stat-label">Pendentes</div>
            </div>
        </div>
        <div class="stat-card stat-card-approved">
            <div class="stat-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $comments->where('status', 'approved')->count() }}</div>
                <div class="stat-label">Aprovados</div>
            </div>
        </div>
        <div class="stat-card stat-card-rejected">
            <div class="stat-icon">
                <i class="fas fa-times-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $comments->where('status', 'rejected')->count() }}</div>
                <div class="stat-label">Rejeitados</div>
            </div>
        </div>
        <div class="stat-card stat-card-total">
            <div class="stat-icon">
                <i class="fas fa-comments"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $comments->total() }}</div>
                <div class="stat-label">Total</div>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="filters-card">
        <div class="filters-header">
            <h3 class="filters-title">
                <i class="fas fa-filter"></i>
                Filtros
            </h3>
        </div>
        <form method="GET" class="filters-form">
            <div class="filters-grid">
                <div class="filter-group">
                    <label class="filter-label">Status</label>
                    <select name="status" class="filter-select">
                        <option value="">Todos os Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                            <i class="fas fa-clock"></i> Pendente
                        </option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                            <i class="fas fa-check"></i> Aprovado
                        </option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                            <i class="fas fa-times"></i> Rejeitado
                        </option>
                    </select>
                </div>
                <div class="filter-group">
                    <label class="filter-label">Post do Blog</label>
                    <select name="blog_post_id" class="filter-select">
                        <option value="">Todos os Posts</option>
                        @foreach($blogPosts as $post)
                            <option value="{{ $post->id }}" {{ request('blog_post_id') == $post->id ? 'selected' : '' }}>
                                {{ $post->title }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="filter-group filter-group-search">
                    <label class="filter-label">Buscar</label>
                    <div class="search-input-wrapper">
                        <i class="fas fa-search search-icon"></i>
                        <input type="text" name="search" class="filter-input" placeholder="Buscar por autor, email ou conteúdo..." value="{{ request('search') }}">
                    </div>
                </div>
            </div>
            <div class="filters-actions">
                <button type="submit" class="btn-filter">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>
                <a href="{{ route('admin.blog-comments.index') }}" class="btn-clear">
                    <i class="fas fa-times"></i>
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Bulk Actions -->
    <div class="bulk-actions-card">
        <form id="bulk-form" method="POST" action="{{ route('admin.blog-comments.bulk-action') }}">
            @csrf
            <div class="bulk-actions-header">
                <h3 class="bulk-actions-title">
                    <i class="fas fa-tasks"></i>
                    Ações em Lote
                </h3>
            </div>
            <div class="bulk-actions-content">
                <div class="bulk-select-group">
                    <select name="action" class="bulk-select" required>
                        <option value="">Selecionar Ação</option>
                        <option value="approve">
                            <i class="fas fa-check"></i> Aprovar Selecionados
                        </option>
                        <option value="reject">
                            <i class="fas fa-times"></i> Rejeitar Selecionados
                        </option>
                        <option value="delete">
                            <i class="fas fa-trash"></i> Excluir Selecionados
                        </option>
                    </select>
                    <button type="submit" class="btn-bulk-execute" onclick="return confirm('Tem certeza que deseja executar esta ação?')">
                        <i class="fas fa-play"></i>
                        Executar Ação
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Comments List -->
    <div class="comments-container">
        <div class="comments-header">
            <div class="comments-title-section">
                <h3 class="comments-title">Lista de Comentários</h3>
                <div class="comments-count">{{ $comments->total() }} comentários encontrados</div>
            </div>
            <div class="select-all-section">
                <label class="select-all-label">
                    <input type="checkbox" id="select-all" class="select-all-checkbox">
                    <span class="select-all-text">Selecionar Todos</span>
                </label>
            </div>
        </div>

        <div class="comments-list">
            @forelse($comments as $comment)
                <div class="comment-card {{ $comment->status === 'pending' ? 'comment-pending' : ($comment->status === 'approved' ? 'comment-approved' : 'comment-rejected') }}">
                    <div class="comment-header">
                        <div class="comment-select">
                            <input type="checkbox" name="comment_ids[]" value="{{ $comment->id }}" class="comment-checkbox" form="bulk-form">
                        </div>
                        <div class="comment-author">
                            <div class="author-name">{{ $comment->author_name }}</div>
                            <div class="author-email">{{ $comment->author_email }}</div>
                            @if($comment->author_website)
                                <div class="author-website">
                                    <a href="{{ $comment->author_website }}" target="_blank" rel="noopener">
                                        <i class="fas fa-external-link-alt"></i>
                                        Website
                                    </a>
                                </div>
                            @endif
                        </div>
                        <div class="comment-meta">
                            <div class="comment-date">
                                <i class="fas fa-calendar"></i>
                                {{ $comment->created_at->format('d/m/Y H:i') }}
                            </div>
                            <div class="comment-post">
                                <i class="fas fa-file-alt"></i>
                                <a href="{{ route('admin.blog-posts.show', $comment->blogPost) }}" target="_blank">
                                    {{ Str::limit($comment->blogPost->title, 40) }}
                                </a>
                            </div>
                            @if($comment->parent_id)
                                <div class="comment-type">
                                    <span class="reply-badge">
                                        <i class="fas fa-reply"></i>
                                        Resposta
                                    </span>
                                </div>
                            @endif
                        </div>
                        <div class="comment-status">
                            @if($comment->status === 'approved')
                                <span class="status-badge status-approved">
                                    <i class="fas fa-check-circle"></i>
                                    Aprovado
                                </span>
                            @elseif($comment->status === 'rejected')
                                <span class="status-badge status-rejected">
                                    <i class="fas fa-times-circle"></i>
                                    Rejeitado
                                </span>
                            @else
                                <span class="status-badge status-pending">
                                    <i class="fas fa-clock"></i>
                                    Pendente
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="comment-content">
                        <p>{{ $comment->content }}</p>
                    </div>

                    <div class="comment-actions">
                        <a href="{{ route('admin.blog-comments.show', $comment) }}" class="action-btn action-view">
                            <i class="fas fa-eye"></i>
                            Visualizar
                        </a>
                        @if($comment->status !== 'approved')
                            <a href="{{ route('admin.blog-comments.approve', $comment) }}" 
                               class="action-btn action-approve" 
                               onclick="return confirm('Aprovar este comentário?')">
                                <i class="fas fa-check"></i>
                                Aprovar
                            </a>
                        @endif
                        @if($comment->status !== 'rejected')
                            <a href="{{ route('admin.blog-comments.reject', $comment) }}" 
                               class="action-btn action-reject" 
                               onclick="return confirm('Rejeitar este comentário?')">
                                <i class="fas fa-times"></i>
                                Rejeitar
                            </a>
                        @endif
                        <a href="{{ route('admin.blog-comments.edit', $comment) }}" class="action-btn action-edit">
                            <i class="fas fa-edit"></i>
                            Editar
                        </a>
                        <form method="POST" action="{{ route('admin.blog-comments.destroy', $comment) }}" style="display: inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="action-btn action-delete" 
                                    onclick="return confirm('Excluir este comentário? Esta ação não pode ser desfeita.')">
                                <i class="fas fa-trash"></i>
                                Excluir
                            </button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-comments"></i>
                    </div>
                    <div class="empty-title">Nenhum comentário encontrado</div>
                    <div class="empty-description">Não há comentários que correspondam aos filtros selecionados.</div>
                </div>
            @endforelse
        </div>
    </div>
    <!-- Pagination -->
    @if($comments->hasPages())
        <div class="pagination-container">
            <div class="pagination-info">
                Mostrando {{ $comments->firstItem() }} a {{ $comments->lastItem() }} de {{ $comments->total() }} comentários
            </div>
            <div class="pagination-links">
                {{ $comments->appends(request()->query())->links() }}
            </div>
        </div>
    @endif
</div>
</div>
</div>
</div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Select all functionality
    const selectAllCheckbox = document.getElementById('select-all');
    const commentCheckboxes = document.querySelectorAll('.comment-checkbox');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            commentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionsVisibility();
        });
    }
    
    // Individual checkbox change
    commentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllState();
            updateBulkActionsVisibility();
        });
    });
    
    function updateSelectAllState() {
        const checkedCount = document.querySelectorAll('.comment-checkbox:checked').length;
        const totalCount = commentCheckboxes.length;
        
        if (selectAllCheckbox) {
            selectAllCheckbox.checked = checkedCount === totalCount && totalCount > 0;
            selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
        }
    }
    
    function updateBulkActionsVisibility() {
        const checkedCount = document.querySelectorAll('.comment-checkbox:checked').length;
        const bulkActionsCard = document.querySelector('.bulk-actions-card');
        
        if (bulkActionsCard) {
            if (checkedCount > 0) {
                bulkActionsCard.classList.add('bulk-actions-visible');
            } else {
                bulkActionsCard.classList.remove('bulk-actions-visible');
            }
        }
    }
    
    // Initialize state
    updateSelectAllState();
    updateBulkActionsVisibility();
});
</script>
@endsection