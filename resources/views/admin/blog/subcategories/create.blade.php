@extends('layouts.admin')

@section('title', 'Nova Subcategoria - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-plus"></i>
                Nova Subcategoria do Blog
            </h1>
            <p class="page-description">Criar uma nova subcategoria para organizar o conteúdo do blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-subcategories.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="form-card">
        <form action="{{ route('admin.blog-subcategories.store') }}" method="POST">
            @csrf
            
            <div class="form-sections">
                <!-- Category Selection -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-folder"></i>
                        Categoria Principal
                    </h3>
                    <p class="section-description">
                        Selecione a categoria à qual esta subcategoria pertence.
                    </p>
                    
                    <div class="form-group">
                        <label for="blog_category_id" class="form-label required">Categoria</label>
                        <select id="blog_category_id" name="blog_category_id" 
                                class="form-select @error('blog_category_id') error @enderror" required>
                            <option value="">Selecione uma categoria</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ old('blog_category_id', request('category')) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                    @if($category->description)
                                        - {{ Str::limit($category->description, 50) }}
                                    @endif
                                </option>
                            @endforeach
                        </select>
                        @error('blog_category_id')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <!-- Basic Information Section -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Informações Básicas
                    </h3>
                    <p class="section-description">
                        Defina o nome, idioma e descrição da subcategoria.
                    </p>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name" class="form-label required">Nome da Subcategoria</label>
                            <input type="text" id="name" name="name" 
                                   value="{{ old('name') }}" 
                                   class="form-input @error('name') error @enderror"
                                   placeholder="Digite o nome da subcategoria" required>
                            @error('name')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="language" class="form-label required">Idioma</label>
                            <select id="language" name="language" 
                                    class="form-select @error('language') error @enderror" required>
                                <option value="">Selecione o idioma</option>
                                <option value="pt" {{ old('language', 'pt') === 'pt' ? 'selected' : '' }}>
                                    🇧🇷 Português
                                </option>
                                <option value="en" {{ old('language') === 'en' ? 'selected' : '' }}>
                                    🇺🇸 Inglês
                                </option>
                                <option value="es" {{ old('language') === 'es' ? 'selected' : '' }}>
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
                        Descrição opcional da subcategoria.
                    </p>
                    
                    <div class="form-group">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea id="description" name="description" 
                                  class="form-textarea @error('description') error @enderror"
                                  rows="3" placeholder="Digite uma descrição para a subcategoria">{{ old('description') }}</textarea>
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
                                   value="{{ old('sort_order', 0) }}" 
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
                                           {{ old('is_active', true) ? 'checked' : '' }}
                                           class="checkbox-input">
                                    <span class="checkbox-custom"></span>
                                    <span class="checkbox-text">Subcategoria ativa</span>
                                </label>
                                <small class="form-help">Subcategorias inativas não aparecerão no site</small>
                            </div>
                            @error('is_active')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Criar Subcategoria
                </button>
                <a href="{{ route('admin.blog-subcategories.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate slug preview from first available name
    const nameInputs = ['name_en', 'name_es', 'name_pt'];
    
    nameInputs.forEach(inputId => {
        const input = document.getElementById(inputId);
        if (input) {
            input.addEventListener('input', function() {
                // This could be enhanced to show a preview of the slug
                console.log('Name changed:', this.value);
            });
        }
    });

    // Pre-select category if passed via URL parameter
    const urlParams = new URLSearchParams(window.location.search);
    const categoryParam = urlParams.get('category');
    if (categoryParam) {
        const categorySelect = document.getElementById('blog_category_id');
        if (categorySelect) {
            categorySelect.value = categoryParam;
        }
    }
});
</script>
@endsection

@section('styles')
<style>
.form-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-top: 1.5rem;
}

.form-sections {
    padding: 2rem;
}

.form-section {
    margin-bottom: 2.5rem;
}

.form-section:last-child {
    margin-bottom: 0;
}

.section-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    font-weight: 600;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-description {
    margin: 0 0 1.5rem 0;
    color: #6c757d;
    line-height: 1.5;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-label.required::after {
    content: '*';
    color: #dc3545;
    margin-left: 0.25rem;
}

.flag-icon {
    font-size: 1.1em;
}

.form-input,
.form-select,
.form-textarea {
    padding: 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 6px;
    font-size: 1rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.form-input.error,
.form-select.error,
.form-textarea.error {
    border-color: #dc3545;
}

.form-help {
    color: #6c757d;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.error-message {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    cursor: pointer;
    font-weight: normal;
}

.checkbox-input {
    margin: 0;
}

.checkbox-custom {
    width: 20px;
    height: 20px;
    border: 2px solid #ced4da;
    border-radius: 4px;
    position: relative;
    transition: all 0.15s ease-in-out;
}

.checkbox-input:checked + .checkbox-custom {
    background: #007bff;
    border-color: #007bff;
}

.checkbox-input:checked + .checkbox-custom::after {
    content: '✓';
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    color: white;
    font-size: 12px;
    font-weight: bold;
}

.checkbox-text {
    color: #495057;
}

.form-actions {
    padding: 1.5rem 2rem;
    background: #f8f9fa;
    border-top: 1px solid #e9ecef;
    display: flex;
    gap: 1rem;
    justify-content: flex-start;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 6px;
    font-size: 1rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
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

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn {
        justify-content: center;
    }
}
</style>
@endsection