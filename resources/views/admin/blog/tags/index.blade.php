@extends('layouts.admin')

@section('title', 'Tags do Blog - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-tags"></i>
                Tags do Blog
            </h1>
            <p class="page-description">Gerencie as tags utilizadas nos posts do blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-tags.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nova Tag
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.blog-tags.index') }}" class="filters-form" id="filtersForm">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="search" class="filter-label">Buscar:</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           value="{{ request('search') }}" 
                           placeholder="Nome da tag..."
                           class="form-control">
                </div>
                
                <div class="filter-group">
                    <label for="status" class="filter-label">Status:</label>
                    <select name="status" id="status" class="form-control" onchange="document.getElementById('filtersForm').submit();">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativas</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativas</option>
                    </select>
                </div>
            </div>
            
            <div class="filters-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>
                <a href="{{ route('admin.blog-tags.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Content -->
    <div class="content-section">
        @if($tags->count() > 0)
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Slug</th>
                            <th>Cor</th>
                            <th>Status</th>
                            <th>Criado em</th>
                            <th class="actions-column">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tags as $tag)
                        <tr>
                            <td>
                                <div class="tag-name">
                                    <span class="tag-badge" style="background-color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                </div>
                            </td>
                            <td>
                                <code class="slug-code">{{ $tag->slug }}</code>
                            </td>
                            <td>
                                <div class="color-display">
                                    <div class="color-swatch" style="background-color: {{ $tag->color }}"></div>
                                    <code class="color-code">{{ $tag->color }}</code>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge {{ $tag->is_active ? 'active' : 'inactive' }}">
                                    {{ $tag->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td>
                                <span class="date-display">{{ $tag->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td class="actions-column">
                                <div class="action-buttons">
                                    <a href="{{ route('admin.blog-tags.show', $tag) }}" 
                                       class="btn btn-sm btn-info" 
                                       title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.blog-tags.edit', $tag) }}" 
                                       class="btn btn-sm btn-primary" 
                                       title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete('{{ $tag->getName() }}', '{{ route('admin.blog-tags.destroy', $tag) }}')"
                                            title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($tags->hasPages())
                <div class="pagination-container">
                    {{ $tags->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <h3 class="empty-state-title">Nenhuma tag encontrada</h3>
                <p class="empty-state-description">
                    @if(request()->hasAny(['search', 'status']))
                        Não foram encontradas tags com os filtros aplicados.
                        <br>
                        <a href="{{ route('admin.blog-tags.index') }}" class="link">Limpar filtros</a>
                    @else
                        Comece criando sua primeira tag para organizar os posts do blog.
                    @endif
                </p>
                @if(!request()->hasAny(['search', 'status']))
                    <a href="{{ route('admin.blog-tags.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Criar Primeira Tag
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3 class="modal-title">Confirmar Exclusão</h3>
            <button type="button" class="modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p>Tem certeza que deseja excluir a tag <strong id="tagName"></strong>?</p>
            <p class="warning-text">Esta ação não pode ser desfeita.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancelar</button>
            <form id="deleteForm" method="POST" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                    Excluir
                </button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.filters-section {
    background: white;
    border-radius: 8px;
    padding: 1.5rem;
    margin: 1.5rem 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.filters-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1rem;
    margin-bottom: 1rem;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-label {
    font-weight: 600;
    color: #495057;
    margin: 0;
}

.form-control {
    padding: 0.5rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 0.875rem;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.filters-actions {
    display: flex;
    gap: 0.5rem;
}

.content-section {
    background: white;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.table-container {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th,
.data-table td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #e9ecef;
}

.data-table th {
    background: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.data-table tbody tr:hover {
    background: #f8f9fa;
}

.actions-column {
    width: 120px;
    text-align: center;
}

.action-buttons {
    display: flex;
    gap: 0.25rem;
    justify-content: center;
}

.tag-name {
    display: flex;
    align-items: center;
}

.tag-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.slug-code {
    background: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 0.8rem;
    color: #495057;
}

.color-display {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.color-swatch {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.color-code {
    background: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 0.8rem;
    color: #495057;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

.date-display {
    color: #6c757d;
    font-size: 0.875rem;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8rem;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

.btn-info {
    background: #17a2b8;
    color: white;
}

.btn-info:hover {
    background: #138496;
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #6c757d;
}

.empty-state-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state-title {
    margin: 0 0 1rem 0;
    color: #495057;
}

.empty-state-description {
    margin: 0 0 2rem 0;
    line-height: 1.5;
}

.link {
    color: #007bff;
    text-decoration: none;
}

.link:hover {
    text-decoration: underline;
}

.pagination-container {
    padding: 1.5rem;
    border-top: 1px solid #e9ecef;
    background: #f8f9fa;
}

/* Modal Styles */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.5);
    z-index: 1000;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-content {
    background: white;
    border-radius: 8px;
    max-width: 500px;
    width: 90%;
    max-height: 90vh;
    overflow-y: auto;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-title {
    margin: 0;
    color: #495057;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.25rem;
    color: #6c757d;
    cursor: pointer;
    padding: 0.25rem;
}

.modal-body {
    padding: 1.5rem;
}

.warning-text {
    color: #dc3545;
    font-size: 0.875rem;
    margin: 0.5rem 0 0 0;
}

.modal-footer {
    padding: 1.5rem;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}

@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .filters-actions {
        flex-direction: column;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .color-display {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .modal-content {
        margin: 1rem;
        width: calc(100% - 2rem);
    }
    
    .modal-footer {
        flex-direction: column;
    }
}
</style>
@endsection

@section('scripts')
<script>
function confirmDelete(tagName, deleteUrl) {
    document.getElementById('tagName').textContent = tagName;
    document.getElementById('deleteForm').action = deleteUrl;
    document.getElementById('deleteModal').style.display = 'flex';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('deleteModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeDeleteModal();
    }
});
</script>
@endsection