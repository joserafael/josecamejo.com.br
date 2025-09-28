@extends('layouts.admin')

@section('title', 'Editar Imagem: ' . $image->title . ' - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-edit"></i>
                Editar Imagem
            </h1>
            <p class="page-description">Editar informações da imagem: {{ $image->title }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-images.show', $image) }}" class="btn btn-outline">
                <i class="fas fa-eye"></i>
                Visualizar
            </a>
            <a href="{{ route('admin.blog-images.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="content-section">
        <form method="POST" action="{{ route('admin.blog-images.update', $image) }}" enctype="multipart/form-data" class="form-container">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <!-- Current Image -->
                <div class="form-group full-width">
                    <label class="form-label">Imagem Atual</label>
                    <div class="current-image-container">
                        <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="current-image">
                        <div class="current-image-info">
                            <p><strong>Arquivo:</strong> {{ $image->original_filename }}</p>
                            <p><strong>Dimensões:</strong> {{ $image->dimensions ?: 'N/A' }}</p>
                            <p><strong>Tamanho:</strong> {{ $image->formatted_file_size }}</p>
                        </div>
                    </div>
                </div>

                <!-- New Image Upload (Optional) -->
                <div class="form-group full-width">
                    <label for="image" class="form-label">Nova Imagem (opcional)</label>
                    <div class="file-upload-area" id="fileUploadArea">
                        <div class="file-upload-content">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h3>Arraste e solte uma nova imagem aqui</h3>
                            <p>ou <span class="file-upload-button">clique para selecionar</span></p>
                            <small>Formatos aceitos: JPEG, PNG, JPG, GIF, SVG, WebP (máx. 10MB)</small>
                        </div>
                        <input type="file" id="image" name="image" accept="image/*" class="file-input">
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
                    <input type="text" id="title" name="title" value="{{ old('title', $image->title) }}" 
                           placeholder="Título da imagem" class="form-input" required>
                    @error('title')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Alt Text -->
                <div class="form-group">
                    <label for="alt_text" class="form-label">Texto Alternativo</label>
                    <input type="text" id="alt_text" name="alt_text" value="{{ old('alt_text', $image->alt_text) }}" 
                           placeholder="Descrição da imagem para acessibilidade" class="form-input">
                    @error('alt_text')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group full-width">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea id="description" name="description" rows="4" 
                              placeholder="Descrição detalhada da imagem" class="form-textarea">{{ old('description', $image->description) }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Language -->
                <div class="form-group">
                    <label for="language" class="form-label required">Idioma</label>
                    <select id="language" name="language" class="form-select" required>
                        <option value="">Selecione o idioma</option>
                        <option value="pt" {{ old('language', $image->language) === 'pt' ? 'selected' : '' }}>Português</option>
                        <option value="en" {{ old('language', $image->language) === 'en' ? 'selected' : '' }}>Inglês</option>
                        <option value="es" {{ old('language', $image->language) === 'es' ? 'selected' : '' }}>Espanhol</option>
                    </select>
                    @error('language')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Sort Order -->
                <div class="form-group">
                    <label for="sort_order" class="form-label">Ordem de Exibição</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $image->sort_order) }}" 
                           min="1" class="form-input">
                    @error('sort_order')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="form-group full-width">
                    <div class="form-checkbox">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                               {{ old('is_active', $image->is_active) ? 'checked' : '' }}>
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
                    Atualizar Imagem
                </button>
                <a href="{{ route('admin.blog-images.show', $image) }}" class="btn btn-outline">
                    <i class="fas fa-eye"></i>
                    Visualizar
                </a>
                <a href="{{ route('admin.blog-images.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.current-image-container {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f8f9fa;
}

.current-image {
    max-width: 200px;
    max-height: 150px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.current-image-info {
    flex: 1;
}

.current-image-info p {
    margin: 0 0 0.5rem 0;
    color: #495057;
}

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
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>
@endsection