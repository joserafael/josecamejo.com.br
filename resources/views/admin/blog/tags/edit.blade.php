@extends('layouts.admin')

@section('title', 'Editar Tag - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-edit"></i>
                Editar Tag: {{ $tag->name }}
            </h1>
            <p class="page-description">Modificar informações da tag do blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-tags.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <a href="{{ route('admin.blog-tags.show', $tag) }}" class="btn btn-info">
                <i class="fas fa-eye"></i>
                Visualizar
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="form-container">
        <form action="{{ route('admin.blog-tags.update', $tag) }}" method="POST" class="admin-form">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <!-- Tag Info -->
                <div class="info-section">
                    <h3 class="section-title">
                        <i class="fas fa-info-circle"></i>
                        Informações da Tag
                    </h3>
                    
                    <div class="info-grid">
                        <div class="info-item">
                            <label class="info-label">Slug:</label>
                            <code class="info-value">{{ $tag->slug }}</code>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Criado em:</label>
                            <span class="info-value">{{ $tag->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="info-item">
                            <label class="info-label">Atualizado em:</label>
                            <span class="info-value">{{ $tag->updated_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                </div>

                    <div class="form-group">
                        <label for="name" class="form-label required">Nome da Tag</label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               value="{{ old('name', $tag->name) }}"
                               class="form-control @error('name') is-invalid @enderror"
                               placeholder="Digite o nome da tag"
                               required>
                        <small class="form-help">Nome único da tag</small>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="language" class="form-label required">Idioma</label>
                        <select id="language" 
                                name="language" 
                                class="form-control @error('language') is-invalid @enderror"
                                required>
                            <option value="">Selecione o idioma</option>
                            <option value="pt" {{ old('language', $tag->language) == 'pt' ? 'selected' : '' }}>🇧🇷 Português</option>
                            <option value="en" {{ old('language', $tag->language) == 'en' ? 'selected' : '' }}>🇺🇸 English</option>
                            <option value="es" {{ old('language', $tag->language) == 'es' ? 'selected' : '' }}>🇪🇸 Español</option>
                        </select>
                        <small class="form-help">Idioma principal da tag</small>
                        @error('language')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                    <div class="form-group">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="3"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Digite uma descrição para a tag (opcional)">{{ old('description', $tag->description) }}</textarea>
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
                                       value="{{ old('color', $tag->color) }}"
                                       class="color-picker @error('color') is-invalid @enderror">
                                <input type="text" 
                                       id="color_text" 
                                       value="{{ old('color', $tag->color) }}"
                                       class="color-text @error('color') is-invalid @enderror"
                                       placeholder="#000000">
                                <button type="button" class="btn btn-sm btn-secondary" onclick="randomColor()">
                                    <i class="fas fa-random"></i>
                                    Aleatória
                                </button>
                            </div>
                            <div class="color-preview">
                                <span class="preview-label">Prévia:</span>
                                <span class="tag-preview" id="tagPreview" style="background-color: {{ $tag->color }}">
                                    {{ $tag->name }}
                                </span>
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
                                           {{ old('is_active', $tag->is_active) ? 'checked' : '' }}
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
                    Salvar Alterações
                </button>
                <a href="{{ route('admin.blog-tags.show', $tag) }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
                <button type="button" 
                        class="btn btn-danger" 
                        onclick="confirmDelete('{{ $tag->getName() }}', '{{ route('admin.blog-tags.destroy', $tag) }}')">
                    <i class="fas fa-trash"></i>
                    Excluir
                </button>
            </div>
        </form>
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

.info-section {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 8px;
    padding: 1.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #e3f2fd;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 600;
    color: #1976d2;
    margin: 0;
}

.info-value {
    color: #0d47a1;
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

.color-preview {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-top: 0.5rem;
}

.preview-label {
    font-size: 0.875rem;
    color: #6c757d;
}

.tag-preview {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
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
    .form-container {
        padding: 1rem;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .info-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
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
// Sync color picker with text input and update preview
function updateColorPreview() {
    const color = document.getElementById('color').value;
    const preview = document.getElementById('tagPreview');
    preview.style.backgroundColor = color;
}

document.getElementById('color').addEventListener('input', function() {
    document.getElementById('color_text').value = this.value;
    updateColorPreview();
});

document.getElementById('color_text').addEventListener('input', function() {
    const colorValue = this.value;
    if (/^#[0-9A-F]{6}$/i.test(colorValue)) {
        document.getElementById('color').value = colorValue;
        updateColorPreview();
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
    updateColorPreview();
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

// Delete confirmation
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