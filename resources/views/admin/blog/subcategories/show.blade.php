@extends('layouts.admin')

@section('title', 'Visualizar Subcategoria - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-eye"></i>
                {{ $subcategory->name }}
            </h1>
            <p class="page-description">Detalhes da subcategoria do blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-subcategories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <a href="{{ route('admin.blog-subcategories.edit', $subcategory) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Editar
            </a>
        </div>
    </div>

    <div class="content-grid">
        <!-- Subcategory Details -->
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Informações da Subcategoria
                </h3>
                <div class="status-badge {{ $subcategory->is_active ? 'active' : 'inactive' }}">
                    {{ $subcategory->is_active ? 'Ativa' : 'Inativa' }}
                </div>
            </div>
            
            <div class="card-content">
                <div class="details-grid">
                    <div class="detail-item">
                        <label class="detail-label">Slug:</label>
                        <code class="detail-value">{{ $subcategory->slug }}</code>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Ordem de Exibição:</label>
                        <span class="detail-value">{{ $subcategory->sort_order }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Criado em:</label>
                        <span class="detail-value">{{ $subcategory->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Atualizado em:</label>
                        <span class="detail-value">{{ $subcategory->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parent Category -->
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-folder"></i>
                    Categoria Principal
                </h3>
                <a href="{{ route('admin.blog-categories.show', ['blog_category' => $subcategory->category->id]) }}" class="btn btn-sm btn-info">
                    <i class="fas fa-eye"></i>
                    Ver Categoria
                </a>
            </div>
            
            <div class="card-content">
                <div class="category-info">
                    <div class="category-header">
                        <h4 class="category-name">{{ $subcategory->category->name }}</h4>
                        <span class="status-badge {{ $subcategory->category->is_active ? 'active' : 'inactive' }}">
                            {{ $subcategory->category->is_active ? 'Ativa' : 'Inativa' }}
                        </span>
                    </div>
                    
                    @if($subcategory->category->description)
                        <p class="category-description">{{ $subcategory->category->description }}</p>
                    @endif
                    
                    <div class="category-details">
                        <div class="detail-row">
                            <span class="detail-label">Slug:</span>
                            <code class="detail-value">{{ $subcategory->category->slug }}</code>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Total de Subcategorias:</span>
                            <span class="detail-value">{{ $subcategory->category->subcategories->count() }}</span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Ordem:</span>
                            <span class="detail-value">{{ $subcategory->category->sort_order }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Name and Language -->
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tag"></i>
                    Nome e Idioma
                </h3>
            </div>
            
            <div class="card-content">
                <div class="details-grid">
                    <div class="detail-item">
                        <label class="detail-label">Nome:</label>
                        <span class="detail-value">{{ $subcategory->name }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Idioma:</label>
                        <div class="language-display">
                            @switch($subcategory->language)
                                @case('pt')
                                    <span class="flag-icon">🇧🇷</span>
                                    <span class="language-name">Português</span>
                                    @break
                                @case('en')
                                    <span class="flag-icon">🇺🇸</span>
                                    <span class="language-name">Inglês</span>
                                    @break
                                @case('es')
                                    <span class="flag-icon">🇪🇸</span>
                                    <span class="language-name">Espanhol</span>
                                    @break
                                @default
                                    <span class="language-name">Não definido</span>
                            @endswitch
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Description -->
        @if($subcategory->description)
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-align-left"></i>
                    Descrição
                </h3>
            </div>
            
            <div class="card-content">
                <div class="description-content">
                    {{ $subcategory->description }}
                </div>
            </div>
        </div>
        @endif

        <!-- Related Subcategories -->
        @if($subcategory->category->subcategories->where('id', '!=', $subcategory->id)->count() > 0)
        <div class="details-card full-width">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-list"></i>
                    Outras Subcategorias da Mesma Categoria
                </h3>
            </div>
            
            <div class="card-content">
                <div class="subcategories-grid">
                    @foreach($subcategory->category->subcategories->where('id', '!=', $subcategory->id)->sortBy('sort_order') as $relatedSubcategory)
                    <div class="subcategory-card">
                        <div class="subcategory-header">
                            <h5 class="subcategory-name">{{ $relatedSubcategory->getName() }}</h5>
                            <span class="status-badge {{ $relatedSubcategory->is_active ? 'active' : 'inactive' }}">
                                {{ $relatedSubcategory->is_active ? 'Ativa' : 'Inativa' }}
                            </span>
                        </div>
                        
                        @if($relatedSubcategory->getDescription())
                            <p class="subcategory-description">{{ Str::limit($relatedSubcategory->getDescription(), 100) }}</p>
                        @endif
                        
                        <div class="subcategory-meta">
                            <span class="meta-item">
                                <i class="fas fa-link"></i>
                                {{ $relatedSubcategory->slug }}
                            </span>
                            <span class="meta-item">
                                <i class="fas fa-sort-numeric-up"></i>
                                Ordem: {{ $relatedSubcategory->sort_order }}
                            </span>
                        </div>
                        
                        <div class="subcategory-actions">
                            <a href="{{ route('admin.blog-subcategories.show', $relatedSubcategory) }}" 
                               class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i>
                                Ver
                            </a>
                            <a href="{{ route('admin.blog-subcategories.edit', $relatedSubcategory) }}" 
                               class="btn btn-sm btn-primary">
                                <i class="fas fa-edit"></i>
                                Editar
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
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

.category-info {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.category-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.category-name {
    margin: 0;
    color: #495057;
    font-size: 1.25rem;
}

.category-description {
    margin: 0;
    color: #6c757d;
    line-height: 1.5;
}

.category-details {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.detail-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.detail-row:last-child {
    border-bottom: none;
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

.subcategories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.subcategory-card {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    background: #f8f9fa;
}

.subcategory-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.subcategory-name {
    margin: 0;
    font-size: 1.1rem;
    color: #495057;
}

.subcategory-description {
    margin: 0 0 1rem 0;
    color: #6c757d;
    line-height: 1.5;
}

.subcategory-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: #6c757d;
}

.subcategory-actions {
    display: flex;
    gap: 0.5rem;
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

@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .detail-item,
    .detail-row {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .category-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .subcategories-grid {
        grid-template-columns: 1fr;
    }
    
    .subcategory-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .subcategory-actions {
        width: 100%;
    }
    
    .subcategory-actions .btn {
        flex: 1;
        justify-content: center;
    }
}
</style>
@endsection