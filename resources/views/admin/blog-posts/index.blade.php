@extends('layouts.admin')

@section('title', 'Posts do Blog - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-blog"></i>
                Posts do Blog
            </h1>
            <p class="page-description">Gerencie todos os posts do seu blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-posts.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Novo Post
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-container">
        <form method="GET" action="{{ route('admin.blog-posts.index') }}" class="filters-form">
            <div class="filters-grid">
                <!-- Search -->
                <div class="filter-group">
                    <label for="search" class="filter-label">Buscar</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" 
                           class="filter-input" placeholder="Título, conteúdo ou autor...">
                </div>

                <!-- Status -->
                <div class="filter-group">
                    <label for="status" class="filter-label">Status</label>
                    <select id="status" name="status" class="filter-select">
                        <option value="">Todos os status</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Rascunho</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Publicado</option>
                        <option value="archived" {{ request('status') === 'archived' ? 'selected' : '' }}>Arquivado</option>
                    </select>
                </div>

                <!-- Category -->
                <div class="filter-group">
                    <label for="category" class="filter-label">Categoria</label>
                    <select id="category" name="category" class="filter-select">
                        <option value="">Todas as categorias</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Language -->
                <div class="filter-group">
                    <label for="language" class="filter-label">Idioma</label>
                    <select id="language" name="language" class="filter-select">
                        <option value="">Todos os idiomas</option>
                        <option value="pt" {{ request('language') === 'pt' ? 'selected' : '' }}>Português</option>
                        <option value="en" {{ request('language') === 'en' ? 'selected' : '' }}>Inglês</option>
                        <option value="es" {{ request('language') === 'es' ? 'selected' : '' }}>Espanhol</option>
                    </select>
                </div>

                <!-- Featured -->
                <div class="filter-group">
                    <label for="featured" class="filter-label">Destaque</label>
                    <select id="featured" name="featured" class="filter-select">
                        <option value="">Todos</option>
                        <option value="1" {{ request('featured') === '1' ? 'selected' : '' }}>Em destaque</option>
                        <option value="0" {{ request('featured') === '0' ? 'selected' : '' }}>Não destacados</option>
                    </select>
                </div>

                <!-- Sort -->
                <div class="filter-group">
                    <label for="sort" class="filter-label">Ordenar por</label>
                    <select id="sort" name="sort" class="filter-select">
                        <option value="created_at_desc" {{ request('sort', 'created_at_desc') === 'created_at_desc' ? 'selected' : '' }}>Mais recentes</option>
                        <option value="created_at_asc" {{ request('sort') === 'created_at_asc' ? 'selected' : '' }}>Mais antigos</option>
                        <option value="title_asc" {{ request('sort') === 'title_asc' ? 'selected' : '' }}>Título A-Z</option>
                        <option value="title_desc" {{ request('sort') === 'title_desc' ? 'selected' : '' }}>Título Z-A</option>
                        <option value="views_desc" {{ request('sort') === 'views_desc' ? 'selected' : '' }}>Mais visualizados</option>
                        <option value="published_at_desc" {{ request('sort') === 'published_at_desc' ? 'selected' : '' }}>Publicação recente</option>
                    </select>
                </div>
            </div>

            <div class="filters-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i>
                    Filtrar
                </button>
                <a href="{{ route('admin.blog-posts.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i>
                    Limpar
                </a>
            </div>
        </form>
    </div>

    <!-- Stats -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $blogPosts->total() }}</div>
                <div class="stat-label">Total de Posts</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon published">
                <i class="fas fa-check-circle"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $blogPosts->where('status', 'published')->count() }}</div>
                <div class="stat-label">Publicados</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon draft">
                <i class="fas fa-edit"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $blogPosts->where('status', 'draft')->count() }}</div>
                <div class="stat-label">Rascunhos</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon featured">
                <i class="fas fa-star"></i>
            </div>
            <div class="stat-content">
                <div class="stat-number">{{ $blogPosts->where('is_featured', true)->count() }}</div>
                <div class="stat-label">Em Destaque</div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="table-container">
        @if($blogPosts->count() > 0)
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Post</th>
                            <th>Categoria</th>
                            <th>Autor</th>
                            <th>Status</th>
                            <th>Idioma</th>
                            <th>Visualizações</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($blogPosts as $post)
                            <tr>
                                <td>
                                    <div class="post-info">
                                        @if($post->featured_image)
                                            <img src="{{ $post->featured_image }}" alt="{{ $post->title }}" class="post-thumbnail">
                                        @else
                                            <div class="post-thumbnail-placeholder">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                        <div class="post-details">
                                            <h4 class="post-title">
                                                <a href="{{ route('admin.blog-posts.show', $post) }}">
                                                    {{ $post->title }}
                                                </a>
                                                @if($post->is_featured)
                                                    <i class="fas fa-star featured-icon" title="Post em destaque"></i>
                                                @endif
                                            </h4>
                                            @if($post->excerpt)
                                                <p class="post-excerpt">{{ Str::limit($post->excerpt, 80) }}</p>
                                            @endif
                                            <div class="post-meta">
                                                <span class="post-slug">{{ $post->slug }}</span>
                                                @if($post->tags && $post->tags->count() > 0)
                                                    <span class="post-tags">
                                                        {{ $post->tags->count() }} tag{{ $post->tags->count() > 1 ? 's' : '' }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="category-info">
                                        <span class="category-name">{{ $post->category->name ?? 'N/A' }}</span>
                                        @if($post->subcategory)
                                            <span class="subcategory-name">{{ $post->subcategory->name }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="author-info">
                                        <span class="author-name">{{ $post->author->name ?? 'N/A' }}</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $post->status }}">
                                        {{ ucfirst($post->status) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="language-badge">
                                        @switch($post->language)
                                            @case('pt') 🇧🇷 PT @break
                                            @case('en') 🇺🇸 EN @break
                                            @case('es') 🇪🇸 ES @break
                                            @default {{ strtoupper($post->language) }}
                                        @endswitch
                                    </span>
                                </td>
                                <td>
                                    <div class="views-info">
                                        <span class="views-count">{{ number_format($post->views_count ?? 0) }}</span>
                                        <span class="reading-time">{{ $post->reading_time ?? 0 }} min</span>
                                    </div>
                                </td>
                                <td>
                                    <div class="date-info">
                                        @if($post->published_at)
                                            <span class="published-date">{{ $post->published_at->format('d/m/Y') }}</span>
                                            <span class="published-time">{{ $post->published_at->format('H:i') }}</span>
                                        @else
                                            <span class="created-date">{{ $post->created_at->format('d/m/Y') }}</span>
                                            <span class="created-time">{{ $post->created_at->format('H:i') }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="actions-dropdown">
                                        <button class="actions-trigger" onclick="toggleDropdown({{ $post->id }})">
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="actions-menu" id="actions-{{ $post->id }}">
                                            <a href="{{ route('admin.blog-posts.show', $post) }}" class="action-item">
                                                <i class="fas fa-eye"></i>
                                                Visualizar
                                            </a>
                                            <a href="{{ route('admin.blog-posts.edit', $post) }}" class="action-item">
                                                <i class="fas fa-edit"></i>
                                                Editar
                                            </a>
                                            <form action="{{ route('admin.blog-posts.toggle-featured', $post) }}" method="POST" style="display: inline;">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="action-item">
                                                    <i class="fas fa-star"></i>
                                                    {{ $post->is_featured ? 'Remover Destaque' : 'Destacar' }}
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.blog-posts.duplicate', $post) }}" method="POST" style="display: inline;">
                                                @csrf
                                                <button type="submit" class="action-item">
                                                    <i class="fas fa-copy"></i>
                                                    Duplicar
                                                </button>
                                            </form>
                                            <div class="action-divider"></div>
                                            <form action="{{ route('admin.blog-posts.destroy', $post) }}" method="POST" 
                                                  onsubmit="return confirm('Tem certeza que deseja excluir este post?')" style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="action-item danger">
                                                    <i class="fas fa-trash"></i>
                                                    Excluir
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-container">
                {{ $blogPosts->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-blog"></i>
                </div>
                <h3>Nenhum post encontrado</h3>
                <p>
                    @if(request()->hasAny(['search', 'status', 'category', 'language', 'featured']))
                        Não encontramos posts com os filtros aplicados.
                        <a href="{{ route('admin.blog-posts.index') }}">Limpar filtros</a>
                    @else
                        Comece criando seu primeiro post do blog.
                    @endif
                </p>
                <a href="{{ route('admin.blog-posts.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Criar Primeiro Post
                </a>
            </div>
        @endif
    </div>
</div>

<style>
.filters-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}

.filters-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.filter-label {
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
}

.filter-input,
.filter-select {
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: border-color 0.2s;
}

.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.filters-actions {
    display: flex;
    gap: 0.75rem;
}

.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    background-color: #e5e7eb;
    color: #6b7280;
    font-size: 1.25rem;
}

.stat-icon.published {
    background-color: #d1fae5;
    color: #065f46;
}

.stat-icon.draft {
    background-color: #fef3c7;
    color: #92400e;
}

.stat-icon.featured {
    background-color: #fef3c7;
    color: #92400e;
}

.stat-number {
    font-size: 1.875rem;
    font-weight: 700;
    color: #111827;
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: #6b7280;
    margin-top: 0.25rem;
}

.table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table-wrapper {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background-color: #f9fafb;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #374151;
    border-bottom: 1px solid #e5e7eb;
    font-size: 0.875rem;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid #f3f4f6;
    vertical-align: top;
}

.data-table tr:hover {
    background-color: #f9fafb;
}

.post-info {
    display: flex;
    gap: 0.75rem;
    align-items: flex-start;
}

.post-thumbnail {
    width: 60px;
    height: 60px;
    object-fit: cover;
    border-radius: 8px;
    flex-shrink: 0;
}

.post-thumbnail-placeholder {
    width: 60px;
    height: 60px;
    background-color: #f3f4f6;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #9ca3af;
    flex-shrink: 0;
}

.post-details {
    flex: 1;
    min-width: 0;
}

.post-title {
    margin: 0 0 0.25rem 0;
    font-size: 0.875rem;
    font-weight: 600;
    line-height: 1.3;
}

.post-title a {
    color: #111827;
    text-decoration: none;
}

.post-title a:hover {
    color: #3b82f6;
}

.featured-icon {
    color: #f59e0b;
    margin-left: 0.25rem;
}

.post-excerpt {
    margin: 0 0 0.5rem 0;
    font-size: 0.75rem;
    color: #6b7280;
    line-height: 1.4;
}

.post-meta {
    display: flex;
    gap: 0.75rem;
    font-size: 0.75rem;
    color: #9ca3af;
}

.post-slug {
    font-family: monospace;
}

.category-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.category-name {
    font-weight: 500;
    color: #374151;
    font-size: 0.875rem;
}

.subcategory-name {
    font-size: 0.75rem;
    color: #6b7280;
}

.author-info {
    font-size: 0.875rem;
    color: #374151;
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

.language-badge {
    font-size: 0.75rem;
    font-weight: 500;
    color: #374151;
}

.views-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.views-count {
    font-weight: 600;
    color: #374151;
    font-size: 0.875rem;
}

.reading-time {
    font-size: 0.75rem;
    color: #6b7280;
}

.date-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.published-date,
.created-date {
    font-weight: 500;
    color: #374151;
    font-size: 0.875rem;
}

.published-time,
.created-time {
    font-size: 0.75rem;
    color: #6b7280;
}

.actions-dropdown {
    position: relative;
}

.actions-trigger {
    background: none;
    border: none;
    padding: 0.5rem;
    border-radius: 6px;
    color: #6b7280;
    cursor: pointer;
    transition: background-color 0.2s;
}

.actions-trigger:hover {
    background-color: #f3f4f6;
    color: #374151;
}

.actions-menu {
    position: absolute;
    top: 100%;
    right: 0;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    min-width: 160px;
    z-index: 10;
    display: none;
}

.actions-menu.show {
    display: block;
}

.action-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    color: #374151;
    text-decoration: none;
    font-size: 0.875rem;
    border: none;
    background: none;
    width: 100%;
    text-align: left;
    cursor: pointer;
    transition: background-color 0.2s;
}

.action-item:hover {
    background-color: #f9fafb;
}

.action-item.danger {
    color: #dc2626;
}

.action-item.danger:hover {
    background-color: #fef2f2;
}

.action-divider {
    height: 1px;
    background-color: #e5e7eb;
    margin: 0.25rem 0;
}

.pagination-container {
    padding: 1.5rem;
    border-top: 1px solid #e5e7eb;
}

.empty-state {
    text-align: center;
    padding: 3rem 1.5rem;
}

.empty-icon {
    font-size: 3rem;
    color: #d1d5db;
    margin-bottom: 1rem;
}

.empty-state h3 {
    margin: 0 0 0.5rem 0;
    color: #374151;
    font-size: 1.25rem;
}

.empty-state p {
    margin: 0 0 1.5rem 0;
    color: #6b7280;
}

.empty-state a {
    color: #3b82f6;
    text-decoration: none;
}

.empty-state a:hover {
    text-decoration: underline;
}

@media (max-width: 768px) {
    .filters-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-container {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .post-info {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .post-thumbnail,
    .post-thumbnail-placeholder {
        width: 100%;
        height: 120px;
    }
}
</style>

<script>
function toggleDropdown(id) {
    // Close all other dropdowns
    document.querySelectorAll('.actions-menu').forEach(menu => {
        if (menu.id !== `actions-${id}`) {
            menu.classList.remove('show');
        }
    });
    
    // Toggle current dropdown
    const menu = document.getElementById(`actions-${id}`);
    menu.classList.toggle('show');
}

// Close dropdowns when clicking outside
document.addEventListener('click', function(event) {
    if (!event.target.closest('.actions-dropdown')) {
        document.querySelectorAll('.actions-menu').forEach(menu => {
            menu.classList.remove('show');
        });
    }
});
</script>
@endsection