@extends('layouts.admin')

@section('title', 'Visualizar Post do Blog - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-eye"></i>
                Visualizar Post do Blog
            </h1>
            <p class="page-description">{{ $blogPost->title }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-posts.edit', $blogPost) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Editar
            </a>
            <form action="{{ route('admin.blog-posts.toggle-featured', $blogPost) }}" method="POST" style="display: inline;">
                @csrf
                @method('PATCH')
                <button type="submit" class="btn {{ $blogPost->is_featured ? 'btn-warning' : 'btn-outline' }}">
                    <i class="fas fa-star"></i>
                    {{ $blogPost->is_featured ? 'Remover Destaque' : 'Destacar' }}
                </button>
            </form>
            <form action="{{ route('admin.blog-posts.duplicate', $blogPost) }}" method="POST" style="display: inline;">
                @csrf
                <button type="submit" class="btn btn-outline">
                    <i class="fas fa-copy"></i>
                    Duplicar
                </button>
            </form>
            <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Content -->
    <div class="show-grid">
        <!-- Main Content -->
        <div class="main-content">
            <!-- Post Content -->
            <div class="content-card">
                <div class="content-header">
                    <div class="status-badge status-{{ $blogPost->status }}">
                        {{ ucfirst($blogPost->status) }}
                    </div>
                    @if($blogPost->is_featured)
                        <div class="featured-badge">
                            <i class="fas fa-star"></i>
                            Destaque
                        </div>
                    @endif
                </div>

                <h1 class="post-title">{{ $blogPost->title }}</h1>
                
                @if($blogPost->excerpt)
                    <div class="post-excerpt">
                        <h3>Resumo</h3>
                        <p>{{ $blogPost->excerpt }}</p>
                    </div>
                @endif

                @if($blogPost->featured_image)
                    <div class="featured-image">
                        <img src="{{ $blogPost->featured_image }}" alt="{{ $blogPost->title }}" class="img-responsive">
                    </div>
                @endif

                <div class="post-content">
                    <h3>Conteúdo</h3>
                    <div class="content-body">
                        {!! nl2br(e($blogPost->content)) !!}
                    </div>
                </div>

                @if($blogPost->meta_title || $blogPost->meta_description)
                    <div class="seo-section">
                        <h3>SEO</h3>
                        @if($blogPost->meta_title)
                            <div class="seo-item">
                                <strong>Meta Título:</strong>
                                <p>{{ $blogPost->meta_title }}</p>
                            </div>
                        @endif
                        @if($blogPost->meta_description)
                            <div class="seo-item">
                                <strong>Meta Descrição:</strong>
                                <p>{{ $blogPost->meta_description }}</p>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Related Media -->
            @if($blogPost->images->count() > 0 || $blogPost->videos->count() > 0)
                <div class="content-card">
                    <h3>Mídia Relacionada</h3>
                    
                    @if($blogPost->images->count() > 0)
                        <div class="media-section">
                            <h4>Imagens ({{ $blogPost->images->count() }})</h4>
                            <div class="media-grid">
                                @foreach($blogPost->images as $image)
                                    <div class="media-item">
                                        @if($image->file_path)
                                            <img src="{{ $image->file_path }}" alt="{{ $image->title }}" class="media-thumbnail">
                                        @endif
                                        <div class="media-info">
                                            <h5>{{ $image->title }}</h5>
                                            @if($image->pivot->caption)
                                                <p class="media-caption">{{ $image->pivot->caption }}</p>
                                            @endif
                                            <small class="media-order">Ordem: {{ $image->pivot->sort_order ?? 0 }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($blogPost->videos->count() > 0)
                        <div class="media-section">
                            <h4>Vídeos ({{ $blogPost->videos->count() }})</h4>
                            <div class="media-list">
                                @foreach($blogPost->videos as $video)
                                    <div class="media-item">
                                        <div class="media-info">
                                            <h5>{{ $video->title }}</h5>
                                            @if($video->description)
                                                <p class="media-description">{{ $video->description }}</p>
                                            @endif
                                            @if($video->pivot->caption)
                                                <p class="media-caption">{{ $video->pivot->caption }}</p>
                                            @endif
                                            @if($video->url)
                                                <a href="{{ $video->url }}" target="_blank" class="media-link">
                                                    <i class="fas fa-external-link-alt"></i>
                                                    Ver Vídeo
                                                </a>
                                            @endif
                                            <small class="media-order">Ordem: {{ $video->pivot->sort_order ?? 0 }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="sidebar-content">
            <!-- Post Information -->
            <div class="info-card">
                <h3>Informações do Post</h3>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">ID:</span>
                        <span class="info-value">#{{ $blogPost->id }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Slug:</span>
                        <span class="info-value">{{ $blogPost->slug }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Status:</span>
                        <span class="info-value status-{{ $blogPost->status }}">
                            {{ ucfirst($blogPost->status) }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Idioma:</span>
                        <span class="info-value">
                            @switch($blogPost->language)
                                @case('pt') Português @break
                                @case('en') Inglês @break
                                @case('es') Espanhol @break
                                @default {{ $blogPost->language }}
                            @endswitch
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Autor:</span>
                        <span class="info-value">{{ $blogPost->author->name }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Visualizações:</span>
                        <span class="info-value">{{ number_format($blogPost->views_count) }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Tempo de Leitura:</span>
                        <span class="info-value">{{ $blogPost->reading_time }} min</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Comentários:</span>
                        <span class="info-value">
                            {{ $blogPost->allow_comments ? 'Permitidos' : 'Não permitidos' }}
                        </span>
                    </div>
                    @if($blogPost->sort_order)
                        <div class="info-item">
                            <span class="info-label">Ordem:</span>
                            <span class="info-value">{{ $blogPost->sort_order }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Dates -->
            <div class="info-card">
                <h3>Datas</h3>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Criado em:</span>
                        <span class="info-value">{{ $blogPost->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Atualizado em:</span>
                        <span class="info-value">{{ $blogPost->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                    @if($blogPost->published_at)
                        <div class="info-item">
                            <span class="info-label">Publicado em:</span>
                            <span class="info-value">{{ $blogPost->published_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Categories -->
            <div class="info-card">
                <h3>Categorização</h3>
                <div class="info-list">
                    <div class="info-item">
                        <span class="info-label">Categoria:</span>
                        <span class="info-value">
                            <a href="{{ route('admin.blog-categories.show', $blogPost->category) }}" class="link">
                                {{ $blogPost->category->name }}
                            </a>
                        </span>
                    </div>
                    @if($blogPost->subcategory)
                        <div class="info-item">
                            <span class="info-label">Subcategoria:</span>
                            <span class="info-value">
                                <a href="{{ route('admin.blog.subcategories.show', $blogPost->subcategory) }}" class="link">
                                    {{ $blogPost->subcategory->name }}
                                </a>
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tags -->
            @if($blogPost->tags->count() > 0)
                <div class="info-card">
                    <h3>Tags ({{ $blogPost->tags->count() }})</h3>
                    <div class="tags-list">
                        @foreach($blogPost->tags as $tag)
                            <a href="{{ route('admin.blog-tags.show', $tag) }}" class="tag-item">
                                {{ $tag->name }}
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Actions -->
            <div class="info-card">
                <h3>Ações</h3>
                <div class="actions-list">
                    <a href="{{ route('admin.blog-posts.edit', $blogPost) }}" class="action-btn btn-primary">
                        <i class="fas fa-edit"></i>
                        Editar Post
                    </a>
                    <form action="{{ route('admin.blog-posts.duplicate', $blogPost) }}" method="POST">
                        @csrf
                        <button type="submit" class="action-btn btn-outline">
                            <i class="fas fa-copy"></i>
                            Duplicar Post
                        </button>
                    </form>
                    <form action="{{ route('admin.blog-posts.destroy', $blogPost) }}" method="POST" 
                          onsubmit="return confirm('Tem certeza que deseja excluir este post?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="action-btn btn-danger">
                            <i class="fas fa-trash"></i>
                            Excluir Post
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.show-grid {
    display: grid;
    grid-template-columns: 1fr 350px;
    gap: 2rem;
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

.content-card,
.info-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
}

.content-header {
    display: flex;
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-draft {
    background-color: #fef3c7;
    color: #92400e;
}

.status-published {
    background-color: #d1fae5;
    color: #065f46;
}

.status-scheduled {
    background-color: #dbeafe;
    color: #1e40af;
}

.featured-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    background-color: #fef3c7;
    color: #92400e;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.post-title {
    font-size: 2rem;
    font-weight: 700;
    color: #1a202c;
    margin-bottom: 1.5rem;
    line-height: 1.2;
}

.post-excerpt {
    margin-bottom: 1.5rem;
    padding: 1rem;
    background-color: #f7fafc;
    border-radius: 8px;
    border-left: 4px solid #3182ce;
}

.post-excerpt h3 {
    margin: 0 0 0.5rem 0;
    font-size: 1.1rem;
    color: #2d3748;
}

.post-excerpt p {
    margin: 0;
    color: #4a5568;
    font-style: italic;
}

.featured-image {
    margin-bottom: 1.5rem;
    text-align: center;
}

.featured-image img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.post-content h3,
.seo-section h3 {
    margin: 0 0 1rem 0;
    font-size: 1.25rem;
    color: #2d3748;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 0.5rem;
}

.content-body {
    line-height: 1.7;
    color: #4a5568;
    font-size: 1rem;
}

.seo-section {
    margin-top: 2rem;
    padding-top: 1.5rem;
    border-top: 1px solid #e2e8f0;
}

.seo-item {
    margin-bottom: 1rem;
}

.seo-item strong {
    display: block;
    margin-bottom: 0.25rem;
    color: #2d3748;
}

.seo-item p {
    margin: 0;
    color: #4a5568;
}

.media-section {
    margin-bottom: 2rem;
}

.media-section h4 {
    margin: 0 0 1rem 0;
    font-size: 1.1rem;
    color: #2d3748;
}

.media-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 1rem;
}

.media-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.media-item {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
    background-color: #f9fafb;
}

.media-thumbnail {
    width: 100%;
    height: 120px;
    object-fit: cover;
    border-radius: 6px;
    margin-bottom: 0.5rem;
}

.media-info h5 {
    margin: 0 0 0.5rem 0;
    font-size: 0.9rem;
    font-weight: 600;
    color: #2d3748;
}

.media-caption,
.media-description {
    margin: 0 0 0.5rem 0;
    font-size: 0.8rem;
    color: #4a5568;
    font-style: italic;
}

.media-link {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    color: #3182ce;
    text-decoration: none;
    font-size: 0.8rem;
    margin-bottom: 0.5rem;
}

.media-link:hover {
    text-decoration: underline;
}

.media-order {
    font-size: 0.75rem;
    color: #718096;
}

.info-card h3 {
    margin: 0 0 1rem 0;
    font-size: 1.1rem;
    color: #2d3748;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 0.5rem;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 0.5rem;
}

.info-label {
    font-weight: 500;
    color: #64748b;
    font-size: 0.875rem;
    min-width: 100px;
}

.info-value {
    font-weight: 600;
    color: #1e293b;
    font-size: 0.875rem;
    text-align: right;
    flex: 1;
}

.link {
    color: #3182ce;
    text-decoration: none;
}

.link:hover {
    text-decoration: underline;
}

.tags-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.tag-item {
    padding: 0.25rem 0.75rem;
    background-color: #e2e8f0;
    color: #4a5568;
    border-radius: 20px;
    font-size: 0.75rem;
    text-decoration: none;
    transition: background-color 0.2s;
}

.tag-item:hover {
    background-color: #cbd5e0;
}

.actions-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    border: none;
    border-radius: 8px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
    width: 100%;
}

.btn-primary {
    background-color: #3182ce;
    color: white;
}

.btn-primary:hover {
    background-color: #2c5aa0;
}

.btn-outline {
    background-color: transparent;
    color: #4a5568;
    border: 1px solid #e2e8f0;
}

.btn-outline:hover {
    background-color: #f7fafc;
}

.btn-danger {
    background-color: #e53e3e;
    color: white;
}

.btn-danger:hover {
    background-color: #c53030;
}

@media (max-width: 768px) {
    .show-grid {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .post-title {
        font-size: 1.5rem;
    }
    
    .media-grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection