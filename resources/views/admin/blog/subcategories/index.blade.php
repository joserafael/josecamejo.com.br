@extends('layouts.admin')

@section('title', 'Subcategorias do Blog - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-list-ul"></i>
                Subcategorias do Blog
            </h1>
            <p class="page-description">Gerencie as subcategorias do seu blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-subcategories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nova Subcategoria
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.blog-subcategories.index') }}" class="filters-form" id="filtersForm">
            <div class="filters-grid">
                <div class="filter-group">
                    <label for="search" class="filter-label">Buscar:</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" 
                           class="filter-input" placeholder="Nome ou descrição...">
                </div>
                
                <div class="filter-group">
                    <label for="status" class="filter-label">Status:</label>
                    <select id="status" name="status" class="filter-select" onchange="document.getElementById('filtersForm').submit()">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Ativas</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inativas</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="category" class="filter-label">Categoria:</label>
                    <select id="category" name="category" class="filter-select" onchange="document.getElementById('filtersForm').submit()">
                        <option value="">Todas as categorias</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <div class="filters-actions">
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>
                <a href="{{ route('admin.blog-subcategories.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i>
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Results -->
    <div class="results-section">
        @if($subcategories->count() > 0)
            <div class="results-header">
                <div class="results-info">
                    <span class="results-count">{{ $subcategories->total() }} subcategoria(s) encontrada(s)</span>
                    @if(request()->hasAny(['search', 'status', 'category']))
                        <span class="results-filtered">
                            <i class="fas fa-filter"></i>
                            Resultados filtrados
                        </span>
                    @endif
                </div>
            </div>

            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Categoria</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Ordem</th>
                            <th>Criado em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($subcategories as $subcategory)
                        <tr>
                            <td>
                                <div class="item-info">
                                    <strong class="item-title">{{ $subcategory->name }}</strong>
                                    @if($subcategory->description)
                                        <small class="item-description">{{ Str::limit($subcategory->description, 80) }}</small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <a href="{{ route('admin.blog-categories.show', ['blog_category' => $subcategory->category->id]) }}" 
                                   class="category-link">
                                    {{ $subcategory->category->name }}
                                </a>
                            </td>
                            <td>
                                <code class="slug-code">{{ $subcategory->slug }}</code>
                            </td>
                            <td>
                                <span class="status-badge {{ $subcategory->is_active ? 'active' : 'inactive' }}">
                                    {{ $subcategory->is_active ? 'Ativa' : 'Inativa' }}
                                </span>
                            </td>
                            <td>
                                <span class="sort-order">{{ $subcategory->sort_order }}</span>
                            </td>
                            <td>
                                <span class="date-info">
                                    {{ $subcategory->created_at->format('d/m/Y') }}
                                    <small>{{ $subcategory->created_at->format('H:i') }}</small>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="{{ route('admin.blog-subcategories.show', $subcategory) }}" 
                                       class="btn btn-sm btn-info" title="Visualizar">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.blog-subcategories.edit', $subcategory) }}" 
                                       class="btn btn-sm btn-primary" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="deleteSubcategory({{ $subcategory->id }})" title="Excluir">
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
            <div class="pagination-wrapper">
                {{ $subcategories->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-list-ul"></i>
                <h3>Nenhuma subcategoria encontrada</h3>
                @if(request()->hasAny(['search', 'status', 'category']))
                    <p>Nenhuma subcategoria corresponde aos filtros aplicados.</p>
                    <a href="{{ route('admin.blog-subcategories.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                        Limpar filtros
                    </a>
                @else
                    <p>Você ainda não criou nenhuma subcategoria.</p>
                    <a href="{{ route('admin.blog-subcategories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Criar primeira subcategoria
                    </a>
                @endif
            </div>
        @endif
    </div>
</div>

<!-- Delete Forms -->
@foreach($subcategories as $subcategory)
<form id="deleteForm{{ $subcategory->id }}" action="{{ route('admin.blog-subcategories.destroy', $subcategory) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endforeach
@endsection

@section('scripts')
<script>
function deleteSubcategory(subcategoryId) {
    if (confirm('Tem certeza que deseja excluir esta subcategoria?\n\nEsta ação não pode ser desfeita.')) {
        document.getElementById('deleteForm' + subcategoryId).submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on Enter key in search input
    document.getElementById('search').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('filtersForm').submit();
        }
    });
});
</script>
@endsection

@section('styles')
<style>
.category-link {
    color: #007bff;
    text-decoration: none;
    font-weight: 500;
}

.category-link:hover {
    text-decoration: underline;
}

.slug-code {
    background: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-family: 'Monaco', 'Menlo', monospace;
    font-size: 0.875rem;
    color: #495057;
}

.sort-order {
    display: inline-block;
    background: #e9ecef;
    padding: 0.25rem 0.5rem;
    border-radius: 12px;
    font-size: 0.875rem;
    font-weight: 500;
    color: #495057;
}

.date-info {
    display: flex;
    flex-direction: column;
    gap: 0.125rem;
}

.date-info small {
    color: #6c757d;
    font-size: 0.75rem;
}

.item-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.item-title {
    color: #495057;
    font-weight: 600;
}

.item-description {
    color: #6c757d;
    line-height: 1.4;
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

.results-filtered {
    color: #007bff;
    font-size: 0.875rem;
    margin-left: 1rem;
}

.filters-grid {
    display: grid;
    grid-template-columns: 2fr 1fr 1fr;
    gap: 1rem;
    align-items: end;
}

@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .action-buttons {
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .action-buttons .btn {
        width: 100%;
        justify-content: center;
    }
}
</style>
@endsection