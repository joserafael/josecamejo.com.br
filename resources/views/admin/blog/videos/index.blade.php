@extends('layouts.admin')

@section('title', 'Vídeos do Blog - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-video"></i>
                Vídeos do Blog
            </h1>
            <p class="page-description">Gerencie os vídeos do blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-videos.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Novo Vídeo
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.blog-videos.index') }}">
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
                            Ativos
                        </option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>
                            Inativos
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
                        <a href="{{ route('admin.blog-videos.index') }}" class="btn btn-outline">
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
        @if($videos->count() > 0)
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Vídeo</th>
                            <th>Título</th>
                            <th>Dimensões</th>
                            <th>Duração</th>
                            <th>Tamanho</th>
                            <th>Idioma</th>
                            <th>Status</th>
                            <th>Ordem</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($videos as $video)
                            <tr>
                                <td>
                                    <div class="video-preview">
                                        @if($video->thumbnail_url)
                                            <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}" 
                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;">
                                        @else
                                            <div style="width: 60px; height: 60px; background: #f0f0f0; border-radius: 4px; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-video text-muted"></i>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="item-info">
                                        <div class="item-title">{{ $video->title }}</div>
                                        <div class="item-meta">{{ $video->slug }}</div>
                                        @if($video->description)
                                            <div class="item-description">{{ Str::limit($video->description, 50) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    @if($video->width && $video->height)
                                        <span class="badge badge-info">{{ $video->dimensions }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($video->duration)
                                        <span class="badge badge-secondary">{{ $video->formatted_duration }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-secondary">{{ $video->formatted_file_size }}</span>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $video->language === 'pt' ? 'success' : ($video->language === 'en' ? 'primary' : 'warning') }}">
                                        {{ strtoupper($video->language) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-{{ $video->is_active ? 'active' : 'inactive' }}">
                                        {{ $video->is_active ? 'Ativo' : 'Inativo' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge badge-outline">{{ $video->sort_order }}</span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.blog-videos.show', $video) }}" 
                                           class="btn btn-sm btn-outline" title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.blog-videos.edit', $video) }}" 
                                           class="btn btn-sm btn-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.blog-videos.destroy', $video) }}" 
                                              style="display: inline;" 
                                              onsubmit="return confirm('Tem certeza que deseja excluir este vídeo?')">
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
                {{ $videos->appends(request()->query())->links() }}
            </div>
        @else
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-video"></i>
                </div>
                <h3 class="empty-state-title">Nenhum vídeo encontrado</h3>
                <p class="empty-state-description">
                    @if(request()->hasAny(['search', 'status', 'language']))
                        Não foram encontrados vídeos com os filtros aplicados.
                    @else
                        Comece fazendo upload do primeiro vídeo do blog.
                    @endif
                </p>
                <div class="empty-state-actions">
                    @if(request()->hasAny(['search', 'status', 'language']))
                        <a href="{{ route('admin.blog-videos.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i>
                            Limpar Filtros
                        </a>
                    @endif
                    <a href="{{ route('admin.blog-videos.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Novo Vídeo
                    </a>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection