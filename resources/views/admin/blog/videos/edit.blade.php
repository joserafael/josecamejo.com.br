@extends('layouts.admin')

@section('title', 'Editar Vídeo: ' . $video->title . ' - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-edit"></i>
                Editar Vídeo
            </h1>
            <p class="page-description">Editar informações do vídeo: {{ $video->title }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-videos.show', $video) }}" class="btn btn-outline">
                <i class="fas fa-eye"></i>
                Visualizar
            </a>
            <a href="{{ route('admin.blog-videos.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="content-section">
        <form method="POST" action="{{ route('admin.blog-videos.update', $video) }}" enctype="multipart/form-data" class="form-container">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <!-- Current Video -->
                <div class="form-group full-width">
                    <label class="form-label">Vídeo Atual</label>
                    <div class="current-video-container">
                        <div class="video-preview">
                            @if($video->thumbnail_url)
                                <img src="{{ $video->thumbnail_url }}" alt="Thumbnail do vídeo" class="video-thumbnail">
                            @else
                                <div class="video-placeholder">
                                    <i class="fas fa-video"></i>
                                </div>
                            @endif
                            <div class="video-overlay">
                                <i class="fas fa-play"></i>
                            </div>
                        </div>
                        <div class="current-video-info">
                            <p><strong>Arquivo:</strong> {{ $video->original_filename }}</p>
                            <p><strong>Dimensões:</strong> {{ $video->dimensions ?: 'N/A' }}</p>
                            <p><strong>Duração:</strong> {{ $video->formatted_duration ?: 'N/A' }}</p>
                            <p><strong>Tamanho:</strong> {{ $video->formatted_file_size }}</p>
                        </div>
                    </div>
                </div>

                <!-- New Video Upload (Optional) -->
                <div class="form-group full-width">
                    <label for="video" class="form-label">Novo Vídeo (opcional)</label>
                    <div class="file-upload-area" id="videoUploadArea">
                        <div class="file-upload-content">
                            <i class="fas fa-video"></i>
                            <h3>Arraste e solte um novo vídeo aqui</h3>
                            <p>ou <span class="file-upload-button">clique para selecionar</span></p>
                            <small>Formatos aceitos: MP4, WebM, AVI, MOV (máx. 100MB)</small>
                        </div>
                        <input type="file" id="video" name="video" accept="video/*" class="file-input">
                    </div>
                    <div id="videoPreview" class="video-preview-container" style="display: none;">
                        <video id="previewVideo" controls class="video-preview">
                            <source src="" type="">
                            Seu navegador não suporta o elemento de vídeo.
                        </video>
                        <button type="button" id="removeVideo" class="btn btn-sm btn-danger remove-video-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @error('video')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- New Thumbnail Upload (Optional) -->
                <div class="form-group full-width">
                    <label for="thumbnail" class="form-label">Nova Thumbnail (opcional)</label>
                    <div class="file-upload-area" id="thumbnailUploadArea">
                        <div class="file-upload-content">
                            <i class="fas fa-image"></i>
                            <h3>Arraste e solte uma nova thumbnail aqui</h3>
                            <p>ou <span class="file-upload-button">clique para selecionar</span></p>
                            <small>Formatos aceitos: JPEG, PNG, JPG, WebP (máx. 5MB)</small>
                        </div>
                        <input type="file" id="thumbnail" name="thumbnail" accept="image/*" class="file-input">
                    </div>
                    <div id="thumbnailPreview" class="thumbnail-preview-container" style="display: none;">
                        <img id="previewThumbnail" src="" alt="Preview" class="thumbnail-preview">
                        <button type="button" id="removeThumbnail" class="btn btn-sm btn-danger remove-thumbnail-btn">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    @error('thumbnail')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Title -->
                <div class="form-group">
                    <label for="title" class="form-label required">Título</label>
                    <input type="text" id="title" name="title" value="{{ old('title', $video->title) }}" 
                           placeholder="Título do vídeo" class="form-input" required>
                    @error('title')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="form-group full-width">
                    <label for="description" class="form-label">Descrição</label>
                    <textarea id="description" name="description" rows="4" 
                              placeholder="Descrição detalhada do vídeo" class="form-textarea">{{ old('description', $video->description) }}</textarea>
                    @error('description')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Language -->
                <div class="form-group">
                    <label for="language" class="form-label required">Idioma</label>
                    <select id="language" name="language" class="form-select" required>
                        <option value="">Selecione o idioma</option>
                        <option value="pt" {{ old('language', $video->language) === 'pt' ? 'selected' : '' }}>Português</option>
                        <option value="en" {{ old('language', $video->language) === 'en' ? 'selected' : '' }}>Inglês</option>
                        <option value="es" {{ old('language', $video->language) === 'es' ? 'selected' : '' }}>Espanhol</option>
                    </select>
                    @error('language')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Sort Order -->
                <div class="form-group">
                    <label for="sort_order" class="form-label">Ordem de Exibição</label>
                    <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $video->sort_order) }}" 
                           min="1" class="form-input">
                    @error('sort_order')
                        <div class="form-error">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="form-group full-width">
                    <div class="form-checkbox">
                        <input type="checkbox" id="is_active" name="is_active" value="1" 
                               {{ old('is_active', $video->is_active) ? 'checked' : '' }}>
                        <label for="is_active" class="checkbox-label">Vídeo ativo</label>
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
                    Atualizar Vídeo
                </button>
                <a href="{{ route('admin.blog-videos.show', $video) }}" class="btn btn-outline">
                    <i class="fas fa-eye"></i>
                    Visualizar
                </a>
                <a href="{{ route('admin.blog-videos.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.current-video-container {
    display: flex;
    gap: 1rem;
    align-items: flex-start;
    padding: 1rem;
    border: 1px solid #ddd;
    border-radius: 8px;
    background-color: #f8f9fa;
}

.video-preview {
    position: relative;
    width: 200px;
    height: 150px;
    border-radius: 8px;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.video-thumbnail {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-placeholder {
    width: 100%;
    height: 100%;
    background-color: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #6c757d;
    font-size: 2rem;
}

.video-overlay {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background-color: rgba(0,0,0,0.7);
    color: white;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.current-video-info {
    flex: 1;
}

.current-video-info p {
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

.video-preview-container,
.thumbnail-preview-container {
    position: relative;
    display: inline-block;
    margin-top: 1rem;
}

.video-preview {
    max-width: 400px;
    max-height: 300px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.thumbnail-preview {
    max-width: 300px;
    max-height: 200px;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

.remove-video-btn,
.remove-thumbnail-btn {
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
    // Video upload functionality
    const videoUploadArea = document.getElementById('videoUploadArea');
    const videoInput = document.getElementById('video');
    const videoPreview = document.getElementById('videoPreview');
    const previewVideo = document.getElementById('previewVideo');
    const removeVideoBtn = document.getElementById('removeVideo');
    
    // Thumbnail upload functionality
    const thumbnailUploadArea = document.getElementById('thumbnailUploadArea');
    const thumbnailInput = document.getElementById('thumbnail');
    const thumbnailPreview = document.getElementById('thumbnailPreview');
    const previewThumbnail = document.getElementById('previewThumbnail');
    const removeThumbnailBtn = document.getElementById('removeThumbnail');
    
    const titleInput = document.getElementById('title');

    // Video drag and drop functionality
    videoUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        videoUploadArea.classList.add('dragover');
    });

    videoUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        videoUploadArea.classList.remove('dragover');
    });

    videoUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        videoUploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            videoInput.files = files;
            handleVideoSelect(files[0]);
        }
    });

    // Video input change
    videoInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleVideoSelect(e.target.files[0]);
        }
    });

    // Remove video
    removeVideoBtn.addEventListener('click', function() {
        videoInput.value = '';
        videoPreview.style.display = 'none';
        videoUploadArea.style.display = 'block';
    });

    // Thumbnail drag and drop functionality
    thumbnailUploadArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        thumbnailUploadArea.classList.add('dragover');
    });

    thumbnailUploadArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        thumbnailUploadArea.classList.remove('dragover');
    });

    thumbnailUploadArea.addEventListener('drop', function(e) {
        e.preventDefault();
        thumbnailUploadArea.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            thumbnailInput.files = files;
            handleThumbnailSelect(files[0]);
        }
    });

    // Thumbnail input change
    thumbnailInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleThumbnailSelect(e.target.files[0]);
        }
    });

    // Remove thumbnail
    removeThumbnailBtn.addEventListener('click', function() {
        thumbnailInput.value = '';
        thumbnailPreview.style.display = 'none';
        thumbnailUploadArea.style.display = 'block';
    });

    function handleVideoSelect(file) {
        if (file && file.type.startsWith('video/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewVideo.src = e.target.result;
                previewVideo.querySelector('source').src = e.target.result;
                previewVideo.querySelector('source').type = file.type;
                videoPreview.style.display = 'block';
                videoUploadArea.style.display = 'none';
                
                // Auto-fill title if empty
                if (!titleInput.value) {
                    const fileName = file.name.replace(/\.[^/.]+$/, "");
                    titleInput.value = fileName.charAt(0).toUpperCase() + fileName.slice(1);
                }
            };
            reader.readAsDataURL(file);
        }
    }

    function handleThumbnailSelect(file) {
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewThumbnail.src = e.target.result;
                thumbnailPreview.style.display = 'block';
                thumbnailUploadArea.style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    }
});
</script>
@endsection