@extends('layouts.admin')

@section('title', 'Editar Categoria - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-edit"></i>
                Editar Categoria do Blog
            </h1>
            <p class="page-description">Editar categoria: {{ $category->getName() }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <a href="{{ route('admin.blog-categories.show', $category) }}" class="btn btn-info">
                <i class="fas fa-eye"></i>
                Visualizar
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="form-card">
        <form action="{{ route('admin.blog-categories.update', $category) }}" method="POST">
            @csrf
            @method('PUT')
            
            <div class="form-sections">
                <!-- Basic Information Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Informações Básicas
                    </h3>
                    <p class="section-description">
                        Informações principais da categoria.
                    </p>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name" class="form-label">
                                Nome da Categoria
                                <span class="required">*</span>
                            </label>
                            <input type="text" id="name" name="name" 
                                   value="{{ old('name', $category->name) }}" 
                                   class="form-input @error('name') error @enderror"
                                   placeholder="Nome da categoria"
                                   required>
                            @error('name')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="language" class="form-label">
                                Idioma
                                <span class="required">*</span>
                            </label>
                            <select id="language" name="language" 
                                    class="form-select @error('language') error @enderror"
                                    required>
                                <option value="">Selecione o idioma</option>
                                <option value="pt" {{ old('language', $category->language) == 'pt' ? 'selected' : '' }}>
                                    🇧🇷 Português
                                </option>
                                <option value="en" {{ old('language', $category->language) == 'en' ? 'selected' : '' }}>
                                    🇺🇸 Inglês
                                </option>
                                <option value="es" {{ old('language', $category->language) == 'es' ? 'selected' : '' }}>
                                    🇪🇸 Espanhol
                                </option>
                            </select>
                            @error('language')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Description Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-align-left"></i>
                        Descrição
                    </h3>
                    <p class="section-description">
                        Descrição opcional da categoria.
                    </p>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">
                            Descrição da Categoria
                        </label>
                        <textarea id="description" name="description" 
                                  class="form-textarea @error('description') error @enderror"
                                  rows="4" placeholder="Descrição da categoria">{{ old('description', $category->description) }}</textarea>
                        @error('description')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Settings Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-cog"></i>
                        Configurações
                    </h3>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="sort_order" class="form-label">Ordem de Exibição</label>
                            <input type="number" id="sort_order" name="sort_order" 
                                   value="{{ old('sort_order', $category->sort_order) }}" 
                                   class="form-input @error('sort_order') error @enderror"
                                   min="0" step="1">
                            <small class="form-help">Ordem para exibição (0 = primeiro)</small>
                            @error('sort_order')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="is_active" value="1" 
                                           {{ old('is_active', $category->is_active) ? 'checked' : '' }}
                                           class="checkbox-input">
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">Categoria ativa</span>
                                </label>
                                <small class="form-help">Categorias inativas não aparecerão no site</small>
                            </div>
                            @error('is_active')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Info Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Informações
                    </h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <label class="info-label">Slug:</label>
                            <code class="info-value">{{ $category->slug }}</code>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Subcategorias:</label>
                            <span class="info-value">{{ $category->subcategories->count() }}</span>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Criado em:</label>
                            <span class="info-value">{{ $category->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Atualizado em:</label>
                            <span class="info-value">{{ $category->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Salvar Alterações
                </button>
                <a href="{{ route('admin.blog-categories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i>
                    Excluir
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Form (hidden) -->
<form id="deleteForm" action="{{ route('admin.blog-categories.destroy', $category) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@section('scripts')
<script>
function confirmDelete() {
    if (confirm('Tem certeza que deseja excluir esta categoria?\n\nEsta ação não pode ser desfeita e pode afetar subcategorias associadas.')) {
        document.getElementById('deleteForm').submit();
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug preview from name
    const nameInput = document.getElementById('name');
    
    if (nameInput) {
        nameInput.addEventListener('input', function() {
            // This could be enhanced to show a preview of the slug
            console.log('Name changed:', this.value);
        });
    }
});
</script>
@endsection