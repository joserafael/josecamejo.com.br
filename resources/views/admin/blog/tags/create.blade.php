@extends('layouts.admin')

@section('title', 'Nova Tag - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-plus"></i>
                Nova Tag
            </h1>
            <p class="page-description">Criar uma nova tag para organizar os posts do blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-tags.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="form-container">
        <form action="{{ route('admin.blog-tags.store') }}" method="POST" class="admin-form">
            @csrf
            
            <div class="form-grid">
                <!-- Basic Information -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Informações Básicas
                    </h3>
                    <p class="section-description">Defina o nome, idioma e descrição da tag</p>
                    
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="name" class="form-label required">Nome da Tag</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Digite o nome da tag" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="language" class="form-label required">Idioma</label>
                            <select id="language" 
                                    name="language" 
                                    class="form-control @error('language') is-invalid @enderror" required>
                                <option value="">Selecione o idioma</option>
                                <option value="pt" {{ old('language') === 'pt' ? 'selected' : '' }}>
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
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Digite uma descrição para a tag (opcional)">{{ old('description') }}</textarea>
                        <small class="form-help">Descrição opcional da tag</small>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Settings -->
                <div class="form-section">
                    <h3 class="section-title">
                        <i class="fas fa-cog"></i>
                        Configurações
                    </h3>
                    
                    <div class="settings-grid">
                        <div class="form-group">
                            <label for="color" class="form-label">
                                <i class="fas fa-palette"></i>
                                Cor da Tag
                            </label>
                            <div class="color-input-group">
                                <input type="color" 
                                       id="color" 
                                       name="color" 
                                       value="{{ old('color', '#007bff') }}"
                                       class="color-picker @error('color') is-invalid @enderror">
                                <input type="text" 
                                       id="color_text" 
                                       value="{{ old('color', '#007bff') }}"
                                       class="color-text @error('color') is-invalid @enderror"
                                       placeholder="#000000">
                                <button type="button" class="btn btn-sm btn-secondary" onclick="randomColor()">
                                    <i class="fas fa-random"></i>
                                    Aleatória
                                </button>
                            </div>
                            <small class="form-help">Cor utilizada para exibir a tag no site</small>
                            @error('color')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-toggle-on"></i>
                                Status
                            </label>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="hidden" name="is_active" value="0">
                                    <input type="checkbox" 
                                           name="is_active" 
                                           value="1" 
                                           {{ old('is_active', '1') ? 'checked' : '' }}
                                           class="checkbox-input">
                                    <span class="checkbox-custom"></span>
                                    Tag ativa
                                </label>
                                <small class="form-help">Tags inativas não aparecem no site</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Criar Tag
                </button>
                <a href="{{ route('admin.blog-tags.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>
@endsection

@section('styles')
<style>
.form-container {
    background: white;
    border-radius: 8px;
    padding: 2rem;
    margin-top: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.form-grid {
    display: grid;
    gap: 2rem;
}

.form-section {
    border: 1px solid #e9ecef;
    border-radius: 8px;
    padding: 1.5rem;
    background: #f8f9fa;
}

.section-title {
    margin: 0 0 0.5rem 0;
    font-size: 1.25rem;
    color: #495057;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.section-description {
    margin: 0 0 1.5rem 0;
    color: #6c757d;
    font-size: 0.875rem;
}

.multilingual-fields {
    display: grid;
    gap: 1.5rem;
}

.language-field {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-label {
    font-weight: 600;
    color: #495057;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.flag-icon {
    font-size: 1.2rem;
}

.form-control {
    padding: 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 0.875rem;
    transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
}

.form-control:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
}

.form-control.is-invalid {
    border-color: #dc3545;
}

.invalid-feedback {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

.form-error {
    background: #f8d7da;
    color: #721c24;
    padding: 0.75rem;
    border-radius: 4px;
    margin-top: 1rem;
    font-size: 0.875rem;
}

.settings-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.color-input-group {
    display: flex;
    gap: 0.5rem;
    align-items: center;
}

.color-picker {
    width: 50px;
    height: 40px;
    border: 1px solid #ced4da;
    border-radius: 4px;
    cursor: pointer;
    padding: 0;
}

.color-text {
    flex: 1;
    padding: 0.5rem 0.75rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 0.875rem;
}

.form-help {
    color: #6c757d;
    font-size: 0.8rem;
    margin: 0;
}

.checkbox-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-weight: normal;
    margin: 0;
}

.checkbox-input {
    display: none;
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

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e9ecef;
}

.btn {
    padding: 0.75rem 1.5rem;
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

@media (max-width: 768px) {
    .form-container {
        padding: 1rem;
    }
    
    .settings-grid {
        grid-template-columns: 1fr;
    }
    
    .color-input-group {
        flex-direction: column;
        align-items: stretch;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>
@endsection

@section('scripts')
<script>
// Sync color picker with text input
document.getElementById('color').addEventListener('input', function() {
    document.getElementById('color_text').value = this.value;
});

document.getElementById('color_text').addEventListener('input', function() {
    const colorValue = this.value;
    if (/^#[0-9A-F]{6}$/i.test(colorValue)) {
        document.getElementById('color').value = colorValue;
    }
});

// Generate random color
function randomColor() {
    const colors = [
        '#007bff', '#28a745', '#dc3545', '#ffc107', '#17a2b8',
        '#6f42c1', '#e83e8c', '#fd7e14', '#20c997', '#6c757d',
        '#343a40', '#f8f9fa', '#e9ecef', '#dee2e6', '#ced4da',
        '#adb5bd', '#868e96', '#495057', '#212529'
    ];
    
    const randomIndex = Math.floor(Math.random() * colors.length);
    const selectedColor = colors[randomIndex];
    
    document.getElementById('color').value = selectedColor;
    document.getElementById('color_text').value = selectedColor;
}

// Form validation
document.querySelector('.admin-form').addEventListener('submit', function(e) {
    const nameEn = document.getElementById('name_en').value.trim();
    const nameEs = document.getElementById('name_es').value.trim();
    const namePt = document.getElementById('name_pt').value.trim();
    
    if (!nameEn && !nameEs && !namePt) {
        e.preventDefault();
        alert('Pelo menos um nome deve ser preenchido.');
        return false;
    }
});
</script>
@endsection