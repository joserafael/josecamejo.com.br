@extends('layouts.admin')

@section('title', 'Imagens do Blog - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-images"></i>
                Imagens do Blog
            </h1>
            <p class="page-description">Gerencie as imagens do blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-images.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nova Imagem
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.blog-images.index') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label for="search" class="form-label">Buscar:</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" 
                           placeholder="Título, descrição ou nome do arquivo..." class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="status" class="form-label">Status:</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>
                            Ativas
                        </option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                            Inativas
                        </option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="language" class="form-label">Idioma:</label>
                    <select id="language" name="language" class="form-select">
                        <option value="">Todos</option>
                        <option value="pt" {{ request('language') === 'pt' ? 'selected' : '' }}>
                            Português
                        </option>
                        <option value="en" {{ request('language') === 'en' ? 'selected' : '' }}>
                            Inglês
                        </option>
                        <option value="es" {{ request('language') === 'es' ? 'selected' : '' }}>
                            Espanhol
                        </option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search"></i>
                            Filtrar
                        </button>
                        <a href="{{ route('admin.blog-images.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i>
                            Limpar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Results -->
    <div class="content-section">
        @if($images->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Imagem</th>
                            <th>Título</th>
                            <th>Dimensões</th>
                            <th>Tamanho</th>
                            <th>Idioma</th>
                            <th>Status</th>
                            <th>Ordem</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($images as $image)
                            <tr>
                                <td>
                                    <div class="image-preview">
                                        <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" 
                                             style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                    </div>
                                </td>
                                <td>
                                    <div class="item-info">
                                        <div class="item-title">{{ $image->title }}</div>
                                        <div class="item-meta">{{ $image->slug }}</div>
                                        @if($image->description)
                                            <div class="item-description">{{ Str::limit($image->description, 50) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($image->width && $image->height)
                                        <span class="badge badge-info">{{ $image->dimensions }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $image->formatted_file_size }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $image->language === 'pt' ? 'success' : ($image->language === 'en' ? 'primary' : 'warning') }}">
                                        {{ strtoupper($image->language) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $image->is_active ? 'active' : 'inactive' }}">
                                        {{ $image->is_active ? 'Ativa' : 'Inativa' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-outline">{{ $image->sort_order }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.blog-images.show', $image) }}" 
                                           class="btn btn-sm btn-outline" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.blog-images.edit', $image) }}" 
                                           class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.blog-images.destroy', $image) }}" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('Tem certeza que deseja excluir esta imagem?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="Excluir">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $images->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-images"></i>
                </div>
                <h3 class="empty-state-title">Nenhuma imagem encontrada</h3>
                <p class="empty-state-description">
                    @if(request()->hasAny(['search', 'status', 'language']))
                        Não foram encontradas imagens com os filtros aplicados.
                    @else
                        Comece fazendo upload da primeira imagem do blog.
                    @endif
                </p>
                <div class="empty-state-actions">
                    @if(request()->hasAny(['search', 'status', 'language']))
                        <a href="{{ route('admin.blog-images.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i>
                            Limpar Filtros
                        </a>
                    @endif
                    <a href="{{ route('admin.blog-images.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Nova Imagem
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection