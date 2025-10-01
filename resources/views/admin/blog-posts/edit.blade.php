@extends('layouts.admin')

@section('title', 'Editar Post do Blog - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-edit"></i>
                Editar Post do Blog
            </h1>
            <p class="page-description">Edite o post: {{ $blogPost->title }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-posts.show', $blogPost) }}" class="btn btn-outline">
                <i class="fas fa-eye"></i>
                Visualizar
            </a>
            <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="form-container">
        <form action="{{ route('admin.blog-posts.update', $blogPost) }}" method="POST" class="blog-post-form">
            @csrf
            @method('PUT')
            
            <div class="form-grid">
                <!-- Main Content -->
                <div class="main-content">
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3>Conteúdo Principal</h3>
                        </div>
                        <div class="form-card-body">
                            <!-- Title -->
                            <div class="form-group">
                                <label for="title" class="form-label required">Título</label>
                                <input type="text" id="title" name="title" value="{{ old('title', $blogPost->title) }}" 
                                       class="form-input @error('title') error @enderror" 
                                       placeholder="Digite o título do post..." required>
                                @error('title')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Slug (readonly) -->
                            <div class="form-group">
                                <label for="slug" class="form-label">Slug</label>
                                <input type="text" id="slug" value="{{ $blogPost->slug }}" 
                                       class="form-input" readonly>
                                <small class="form-help">O slug é gerado automaticamente baseado no título</small>
                            </div>

                            <!-- Excerpt -->
                            <div class="form-group">
                                <label for="excerpt" class="form-label">Resumo</label>
                                <textarea id="excerpt" name="excerpt" rows="3" 
                                          class="form-textarea @error('excerpt') error @enderror" 
                                          placeholder="Breve resumo do post (opcional)...">{{ old('excerpt', $blogPost->excerpt) }}</textarea>
                                @error('excerpt')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                                <small class="form-help">Máximo 500 caracteres</small>
                            </div>

                            <!-- Content -->
                            <div class="form-group">
                                <label for="content" class="form-label required">Conteúdo</label>
                                <textarea id="content" name="content" rows="15" 
                                          class="form-textarea @error('content') error @enderror" 
                                          placeholder="Escreva o conteúdo do post..." required>{{ old('content', $blogPost->content) }}</textarea>
                                @error('content')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Featured Image -->
                            <div class="form-group">
                                <label for="featured_image" class="form-label">Imagem Destacada</label>
                                <input type="text" id="featured_image" name="featured_image" value="{{ old('featured_image', $blogPost->featured_image) }}" 
                                       class="form-input @error('featured_image') error @enderror" 
                                       placeholder="URL da imagem destacada...">
                                @error('featured_image')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- SEO -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3>SEO</h3>
                        </div>
                        <div class="form-card-body">
                            <!-- Meta Title -->
                            <div class="form-group">
                                <label for="meta_title" class="form-label">Meta Título</label>
                                <input type="text" id="meta_title" name="meta_title" value="{{ old('meta_title', $blogPost->meta_title) }}" 
                                       class="form-input @error('meta_title') error @enderror" 
                                       placeholder="Título para SEO (opcional)...">
                                @error('meta_title')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                                <small class="form-help">Máximo 255 caracteres</small>
                            </div>

                            <!-- Meta Description -->
                            <div class="form-group">
                                <label for="meta_description" class="form-label">Meta Descrição</label>
                                <textarea id="meta_description" name="meta_description" rows="3" 
                                          class="form-textarea @error('meta_description') error @enderror" 
                                          placeholder="Descrição para SEO (opcional)...">{{ old('meta_description', $blogPost->meta_description) }}</textarea>
                                @error('meta_description')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                                <small class="form-help">Máximo 500 caracteres</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="sidebar-content">
                    <!-- Post Info -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3>Informações do Post</h3>
                        </div>
                        <div class="form-card-body">
                            <div class="info-grid">
                                <div class="info-item">
                                    <span class="info-label">Criado em:</span>
                                    <span class="info-value">{{ $blogPost->created_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Atualizado em:</span>
                                    <span class="info-value">{{ $blogPost->updated_at->format('d/m/Y H:i') }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Autor:</span>
                                    <span class="info-value">{{ $blogPost->author->name }}</span>
                                </div>
                                <div class="info-item">
                                    <span class="info-label">Visualizações:</span>
                                    <span class="info-value">{{ number_format($blogPost->views_count) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Publish Settings -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3>Configurações de Publicação</h3>
                        </div>
                        <div class="form-card-body">
                            <!-- Status -->
                            <div class="form-group">
                                <label for="status" class="form-label required">Status</label>
                                <select id="status" name="status" class="form-select @error('status') error @enderror" required>
                                    <option value="draft" {{ old('status', $blogPost->status) === 'draft' ? 'selected' : '' }}>Rascunho</option>
                                    <option value="published" {{ old('status', $blogPost->status) === 'published' ? 'selected' : '' }}>Publicado</option>
                                    <option value="scheduled" {{ old('status', $blogPost->status) === 'scheduled' ? 'selected' : '' }}>Agendado</option>
                                </select>
                                @error('status')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Published At -->
                            <div class="form-group" id="published-at-group">
                                <label for="published_at" class="form-label">Data de Publicação</label>
                                <input type="datetime-local" id="published_at" name="published_at" 
                                       value="{{ old('published_at', $blogPost->published_at ? $blogPost->published_at->format('Y-m-d\TH:i') : '') }}" 
                                       class="form-input @error('published_at') error @enderror">
                                @error('published_at')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Language -->
                            <div class="form-group">
                                <label for="language" class="form-label required">Idioma</label>
                                <select id="language" name="language" class="form-select @error('language') error @enderror" required>
                                    <option value="pt" {{ old('language', $blogPost->language) === 'pt' ? 'selected' : '' }}>Português</option>
                                    <option value="en" {{ old('language', $blogPost->language) === 'en' ? 'selected' : '' }}>Inglês</option>
                                    <option value="es" {{ old('language', $blogPost->language) === 'es' ? 'selected' : '' }}>Espanhol</option>
                                </select>
                                @error('language')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Options -->
                            <div class="form-group">
                                <div class="checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="is_featured" value="1" {{ old('is_featured', $blogPost->is_featured) ? 'checked' : '' }}>
                                        <span class="checkbox-custom"></span>
                                        Post em destaque
                                    </label>
                                </div>
                            </div>

                            <div class="form-group">
                                <div class="checkbox-group">
                                    <label class="checkbox-label">
                                        <input type="checkbox" name="allow_comments" value="1" {{ old('allow_comments', $blogPost->allow_comments) ? 'checked' : '' }}>
                                        <span class="checkbox-custom"></span>
                                        Permitir comentários
                                    </label>
                                </div>
                            </div>

                            <!-- Sort Order -->
                            <div class="form-group">
                                <label for="sort_order" class="form-label">Ordem de Classificação</label>
                                <input type="number" id="sort_order" name="sort_order" value="{{ old('sort_order', $blogPost->sort_order) }}" 
                                       class="form-input @error('sort_order') error @enderror" min="0">
                                @error('sort_order')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3>Categorização</h3>
                        </div>
                        <div class="form-card-body">
                            <!-- Category -->
                            <div class="form-group">
                                <label for="blog_category_id" class="form-label required">Categoria</label>
                                <select id="blog_category_id" name="blog_category_id" class="form-select @error('blog_category_id') error @enderror" required>
                                    <option value="">Selecione uma categoria</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('blog_category_id', $blogPost->blog_category_id) == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('blog_category_id')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Subcategory -->
                            <div class="form-group">
                                <label for="blog_subcategory_id" class="form-label">Subcategoria</label>
                                <select id="blog_subcategory_id" name="blog_subcategory_id" class="form-select @error('blog_subcategory_id') error @enderror">
                                    <option value="">Selecione uma subcategoria</option>
                                    @foreach($subcategories as $subcategory)
                                        <option value="{{ $subcategory->id }}" {{ old('blog_subcategory_id', $blogPost->blog_subcategory_id) == $subcategory->id ? 'selected' : '' }}>
                                            {{ $subcategory->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('blog_subcategory_id')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Tags -->
                            <div class="form-group">
                                <label for="tags" class="form-label">Tags</label>
                                <div class="tags-container">
                                    @foreach($tags as $tag)
                                        <label class="tag-checkbox">
                                            <input type="checkbox" name="tags[]" value="{{ $tag->id }}" 
                                                   {{ in_array($tag->id, old('tags', $blogPost->tags->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <span class="tag-label">{{ $tag->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('tags')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Media -->
                    <div class="form-card">
                        <div class="form-card-header">
                            <h3>Mídia</h3>
                        </div>
                        <div class="form-card-body">
                            <!-- Images -->
                            <div class="form-group">
                                <label for="images" class="form-label">Imagens</label>
                                <div class="media-container">
                                    @foreach($images as $image)
                                        <label class="media-checkbox">
                                            <input type="checkbox" name="images[]" value="{{ $image->id }}" 
                                                   {{ in_array($image->id, old('images', $blogPost->images->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <span class="media-label">{{ $image->title }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('images')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Videos -->
                            <div class="form-group">
                                <label for="videos" class="form-label">Vídeos</label>
                                <div class="media-container">
                                    @foreach($videos as $video)
                                        <label class="media-checkbox">
                                            <input type="checkbox" name="videos[]" value="{{ $video->id }}" 
                                                   {{ in_array($video->id, old('videos', $blogPost->videos->pluck('id')->toArray())) ? 'checked' : '' }}>
                                            <span class="media-label">{{ $video->title }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('videos')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Atualizar Post
                </button>
                <a href="{{ route('admin.blog-posts.show', $blogPost) }}" class="btn btn-outline">
                    <i class="fas fa-eye"></i>
                    Visualizar
                </a>
                <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

<style>
.blog-post-form {
    max-width: none;
}

.form-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
    margin-bottom: 2rem;
}

.main-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.sidebar-content {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.form-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.form-card-header {
    background: #f8f9fa;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
}

.form-card-header h3 {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #2d3748;
}

.form-card-body {
    padding: 1.5rem;
}

.info-grid {
    display: grid;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f5f9;
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-weight: 500;
    color: #64748b;
    font-size: 0.875rem;
}

.info-value {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.875rem;
}

.tags-container,
.media-container {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    max-height: 200px;
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem;
}

.tag-checkbox,
.media-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    padding: 0.25rem;
    border-radius: 4px;
    transition: background-color 0.2s;
}

.tag-checkbox:hover,
.media-checkbox:hover {
    background-color: #f7fafc;
}

.tag-label,
.media-label {
    font-size: 0.875rem;
    color: #4a5568;
}

.checkbox-group {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.checkbox-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    font-size: 0.875rem;
    color: #4a5568;
}

.checkbox-custom {
    width: 18px;
    height: 18px;
    border: 2px solid #e2e8f0;
    border-radius: 4px;
    position: relative;
    transition: all 0.2s;
}

.checkbox-label input[type="checkbox"] {
    display: none;
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom {
    background-color: #3182ce;
    border-color: #3182ce;
}

.checkbox-label input[type="checkbox"]:checked + .checkbox-custom::after {
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
    justify-content: flex-start;
    padding: 1.5rem;
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

@media (max-width: 768px) {
    .form-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const statusSelect = document.getElementById('status');
    const publishedAtGroup = document.getElementById('published-at-group');
    
    function togglePublishedAt() {
        if (statusSelect.value === 'scheduled') {
            publishedAtGroup.style.display = 'block';
            document.getElementById('published_at').required = true;
        } else {
            publishedAtGroup.style.display = 'none';
            document.getElementById('published_at').required = false;
        }
    }
    
    statusSelect.addEventListener('change', togglePublishedAt);
    togglePublishedAt(); // Initial check
});
</script>
@endsection