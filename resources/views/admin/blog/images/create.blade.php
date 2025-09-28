@extends('layouts.admin')

@section('title', 'Nova Imagem do Blog - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-plus"></i>
                Nova Imagem do Blog
            </h1>
            <p class="page-description">Fazer upload de uma nova imagem para o blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-images.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="content-section">
        <form method="POST" action="{{ route('admin.blog-images.store') }}" enctype="multipart/form-data" class="form-container">
            @csrf
            
            <div class="form-grid">
                <!-- Image Upload -->
                <div class="form-group full-width">
                    <label for="image" class="form-label required">Imagem</label>
                    <div class="file-upload-area" id="fileUploadArea">
                        <div class="file-upload-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h3>Arraste e solte sua imagem aqui</h3>
                            <p>ou <span class="file-upload-button">clique para selecionar</span></p>
                            <small>Formatos aceitos: JPEG, PNG, JPG, GIF, SVG, WebP (máx. 10MB)</small>
                        </div>
                        <input type="file" id="image" name="image" accept="image/*" class="file-input" required>
                    </div>
                    <div id="imagePreview" class="image-preview-container" style="display: none;">
                        <img id="previewImg" src="" alt="Preview" class="image-preview">
                        <button type="button" id="removeImage" class="btn btn-sm btn-danger remove-image-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @error('image')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Title -->
                <div class="form-group">
                    <label for="title" class="form-label required">Título</label>
                    <input type="text" id="title" name="title" value="{{ old('title') }}" 
                           placeholder="Título da imagem" class="form-input" required>
                    @error('title')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Alt Text -->
                <div class="form-group">
                    <label for="alt_text" class="form-label">Texto Alternativo</label>
                    <input type="text" id="alt_text" name="alt_text" value="{{ old('alt_text') }}" 
                           placeholder="Descrição da imagem para acessibilidade" class="form-input">
                    @error('alt_text')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group full-width">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea id="description" name="description" rows="4" 
                              placeholder="Descrição detalhada da imagem" class="form-textarea">{{ old('description') }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Language -->
                <div class="form-group">
                    <label for="language" class="form-label required">Idioma</label>
                    <select id="language" name="language" class="form-select" required>
                        <option value="">Selecione o idioma</option>
                        <option value="pt" {{ old('language') === 'pt' ? 'selected' : '' }}>Português</option>
                        <option value="en" {{ old('language') === 'en' ? 'selected' : '' }}>Inglês</option>
                        <option value="es" {{ old('language') === 'es' ? 'selected' : '' }}>Espanhol</option>
                    </select>
                    @error('language')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Sort Order -->
                <div class="form-group">
                    <label for="sort_order" class="form-label">Ordem de Exibição</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', 1) }}" 
                           min="1" class="form-input">
                    @error('sort_order')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="form-group full-width">
                    <div class="form-checkbox">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                               {{ old('is_active', true) ? 'checked' : '' }}>
                        <label for="is_active" class="checkbox-label">Imagem ativa</label>
                    </div>
                    @error('is_active')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Salvar Imagem
                </button>
                <a href="{{ route('admin.blog-images.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.file-upload-area {
    border: 2px dashed #ddd;
    border-radius: 8px;
    padding: 2rem;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.file-upload-area:hover,
.file-upload-area.dragover {
    border-color: #007bff;
    background-color: #f8f9fa;
}

.file-upload-content i {
    font-size: 3rem;
    color: #6c757d;
    margin-bottom: 1rem;
}

.file-upload-content h3 {
    margin: 0 0 0.5rem 0;
    color: #495057;
}

.file-upload-content p {
    margin: 0 0 0.5rem 0;
    color: #6c757d;
}

.file-upload-button {
    color: #007bff;
    text-decoration: underline;
    cursor: pointer;
}

.file-input {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    opacity: 0;
    cursor: pointer;
}

.image-preview-container {
    position: relative;
    display: inline-block;
    margin-top: 1rem;
}

.image-preview {
    max-width: 300px;
    max-height: 200px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.remove-image-btn {
    position: absolute;
    top: -8px;
    right: -8px;
    border-radius: 50%;
    width: 24px;
    height: 24px;
    padding: 0;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileUploadArea = document.getElementById('fileUploadArea');
    const fileInput = document.getElementById('image');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImageBtn = document.getElementById('removeImage');
    const titleInput = document.getElementById('title');
    const altTextInput = document.getElementById('alt_text');

    // Drag and drop functionality
    fileUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        fileUploadArea.classList.add('dragover');
    });

    fileUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');
    });

    fileUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        fileUploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            handleFileSelect(files[0]);
        }
    });

    // File input change
    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    // Remove image
    removeImageBtn.addEventListener('click', function() {
        fileInput.value = '';
        imagePreview.style.display = 'none';
        fileUploadArea.style.display = 'block';
    });

    function handleFileSelect(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
                fileUploadArea.style.display = 'none';
                
                // Auto-fill title if empty
                if (!titleInput.value) {
                    const fileName = file.name.replace(/\.[^/.]+$/, ""); // Remove extension
                    titleInput.value = fileName.replace(/[_-]/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
                }
                
                // Auto-fill alt text if empty
                if (!altTextInput.value) {
                    altTextInput.value = titleInput.value;
                }
            };
            reader.readAsDataURL(file);
        }
    }

    // Auto-fill alt text when title changes
    titleInput.addEventListener('input', function() {
        if (!altTextInput.value || altTextInput.value === titleInput.dataset.oldValue) {
            altTextInput.value = titleInput.value;
        }
        titleInput.dataset.oldValue = titleInput.value;
    });
});
</script>
@endsection