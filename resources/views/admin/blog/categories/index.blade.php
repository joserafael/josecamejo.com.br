@extends('layouts.admin')

@section('title', 'Categorias do Blog - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-folder"></i>
                Categorias do Blog
            </h1>
            <p class="page-description">Gerencie as categorias do blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nova Categoria
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.blog-categories.index') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label for="search" class="form-label">Buscar:</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" 
                           placeholder="Nome ou slug..." class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="status" class="form-label">Status:</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                            Ativas
                        </option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                            Inativas
                        </option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search"></i>
                            Filtrar
                        </button>
                        <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i>
                            Limpar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Categories Table -->
    <div class="table-card">
        @if($categories->count() > 0)
            <div class="table-header">
                <div class="table-info">
                    <span class="results-count">
                        {{ $categories->total() }} categoria(s) encontrada(s)
                    </span>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Slug</th>
                            <th>Status</th>
                            <th>Ordem</th>
                            <th>Subcategorias</th>
                            <th>Criado em</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>
                                    <div class="category-info">
                                        <div class="category-name">
                                            {{ $category->name }}
                                        </div>
                                        @if($category->description)
                                            <div class="category-description">
                                                {{ Str::limit($category->description, 100) }}
                                            </div>
                                        @endif
                                        <div class="category-languages">
                                            @if($category->name_en)
                                                <span class="badge badge-info">EN</span>
                                            @endif
                                            @if($category->name_es)
                                                <span class="badge badge-info">ES</span>
                                            @endif
                                            @if($category->name_pt)
                                                <span class="badge badge-info">PT</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <code>{{ $category->slug }}</code>
                                </td>
                                <td>
                                    @if($category->is_active)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i>
                                            Ativa
                                        </span>
                                    @else
                                        <span class="badge badge-danger">
                                            <i class="fas fa-times"></i>
                                            Inativa
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <span class="sort-order">{{ $category->sort_order }}</span>
                                </td>
                                <td>
                                    <span class="subcategories-count">
                                        {{ $category->subcategories->count() }}
                                    </span>
                                </td>
                                <td>
                                    <span class="date">{{ $category->created_at->format('d/m/Y H:i') }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.blog-categories.show', $category) }}" 
                                           class="btn btn-sm btn-info" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.blog-categories.edit', $category) }}" 
                                           class="btn btn-sm btn-warning" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('admin.blog-categories.destroy', $category) }}" 
                                              method="POST" style="display: inline-block;" 
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta categoria? Esta ação não pode ser desfeita.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $categories->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3 class="empty-state-title">Nenhuma categoria encontrada</h3>
                <p class="empty-state-description">
                    @if(request('search') || request('status'))
                        Nenhuma categoria corresponde aos filtros aplicados.
                    @else
                        Comece criando sua primeira categoria do blog.
                    @endif
                </p>
                <div class="empty-state-actions">
                    @if(request('search') || request('status'))
                        <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Limpar Filtros
                        </a>
                    @endif
                    <a href="{{ route('admin.blog-categories.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Nova Categoria
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-submit form on filter change
    const statusSelect = document.getElementById('status');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            this.form.submit();
        });
    }
});
</script>
@endsection