@extends('layouts.admin')

@section('title', 'Visualizar Categoria - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-eye"></i>
                {{ $category->name }}
            </h1>
            <p class="page-description">Detalhes da categoria do blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <a href="{{ route('admin.blog-categories.edit', $category) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Editar
            </a>
        </div>
    </div>

    <div class="content-grid">
        <!-- Category Details -->
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Informações da Categoria
                </h3>
                <div class="status-badge {{ $category->is_active ? 'active' : 'inactive' }}">
                    {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                </div>
            </div>
            
            <div class="card-content">
                <div class="details-grid">
                    <div class="detail-item">
                        <label class="detail-label">Slug:</label>
                        <code class="detail-value">{{ $category->slug }}</code>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Ordem de Exibição:</label>
                        <span class="detail-value">{{ $category->sort_order }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Total de Subcategorias:</label>
                        <span class="detail-value">{{ $category->subcategories->count() }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Criado em:</label>
                        <span class="detail-value">{{ $category->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Atualizado em:</label>
                        <span class="detail-value">{{ $category->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Content -->
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-language"></i>
                    Conteúdo da Categoria
                </h3>
            </div>
            
            <div class="card-content">
                <div class="details-grid">
                    <div class="detail-item">
                        <label class="detail-label">Nome:</label>
                        <span class="detail-value">{{ $category->name }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Idioma:</label>
                        <span class="detail-value">
                            @switch($category->language)
                                @case('pt')
                                    🇧🇷 Português
                                    @break
                                @case('en')
                                    🇺🇸 Inglês
                                    @break
                                @case('es')
                                    🇪🇸 Espanhol
                                    @break
                                @default
                                    {{ $category->language }}
                            @endswitch
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Category Description -->
        @if($category->description)
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-align-left"></i>
                    Descrição
                </h3>
            </div>
            
            <div class="card-content">
                <div class="description-content">
                    {{ $category->description }}
                </div>
            </div>
        </div>
        @endif

        <!-- Subcategories -->
        @if($category->subcategories->count() > 0)
        <div class="details-card full-width">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Subcategorias ({{ $category->subcategories->count() }})
                </h3>
                <a href="{{ route('admin.blog-subcategories.create') }}?category={{ $category->id }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i>
                    Nova Subcategoria
                </a>
            </div>
            
            <div class="card-content">
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Slug</th>
                                <th>Status</th>
                                <th>Ordem</th>
                                <th>Criado em</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($category->subcategories->sortBy('sort_order') as $subcategory)
                            <tr>
                                <td>
                                    <strong>{{ $subcategory->name }}</strong>
                                    @if($subcategory->description)
                                        <br>
                                        <small class="text-muted">{{ Str::limit($subcategory->description, 60) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <code>{{ $subcategory->slug }}</code>
                                </td>
                                <td>
                                    <span class="status-badge {{ $subcategory->is_active ? 'active' : 'inactive' }}">
                                        {{ $subcategory->is_active ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </td>
                                <td>{{ $subcategory->sort_order }}</td>
                                <td>{{ $subcategory->created_at->format('d/m/Y') }}</td>
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
            </div>
        </div>
        @else
        <div class="details-card full-width">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Subcategorias
                </h3>
                <a href="{{ route('admin.blog-subcategories.create') }}?category={{ $category->id }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i>
                    Nova Subcategoria
                </a>
            </div>
            
            <div class="card-content">
                <div class="empty-state">
                    <i class="fas fa-list-ul"></i>
                    <h4>Nenhuma subcategoria encontrada</h4>
                    <p>Esta categoria ainda não possui subcategorias.</p>
                    <a href="{{ route('admin.blog-subcategories.create') }}?category={{ $category->id }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Criar primeira subcategoria
                    </a>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<!-- Delete Forms for Subcategories -->
@foreach($category->subcategories as $subcategory)
<form id="deleteSubcategoryForm{{ $subcategory->id }}" 
      action="{{ route('admin.blog-subcategories.destroy', $subcategory) }}" 
      method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endforeach
@endsection

@section('scripts')
<script>
function deleteSubcategory(subcategoryId) {
    if (confirm('Tem certeza que deseja excluir esta subcategoria?\n\nEsta ação não pode ser desfeita.')) {
        document.getElementById('deleteSubcategoryForm' + subcategoryId).submit();
    }
}
</script>
@endsection

@section('styles')
<style>
.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.details-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.details-card.full-width {
    grid-column: 1 / -1;
}

.card-header {
    background: #f8f9fa;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
}

.card-content {
    padding: 1.5rem;
}

.details-grid {
    display: grid;
    gap: 1rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #6c757d;
    margin: 0;
}

.detail-value {
    color: #495057;
}

.multilingual-grid {
    display: grid;
    gap: 1rem;
}

.language-item {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    overflow: hidden;
}

.language-header {
    background: #f8f9fa;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #495057;
}

.language-content {
    padding: 1rem;
    color: #6c757d;
}

.language-content.description {
    white-space: pre-wrap;
    line-height: 1.5;
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

.empty-state {
    text-align: center;
    padding: 3rem 1rem;
    color: #6c757d;
}

.empty-state i {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.empty-state h4 {
    margin-bottom: 0.5rem;
    color: #495057;
}

.empty-state p {
    margin-bottom: 1.5rem;
}

@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .detail-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
}
</style>
@endsection