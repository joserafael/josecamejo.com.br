@extends('layouts.admin')

@section('title', 'Vídeo: ' . $video->title . ' - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-video"></i>
                {{ $video->title }}
            </h1>
            <p class="page-description">Detalhes do vídeo do blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-videos.edit', $video) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Editar
            </a>
            <a href="{{ route('admin.blog-videos.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <form method="POST" action="{{ route('admin.blog-videos.destroy', $video) }}" 
                  style="display: inline;" 
                  onsubmit="return confirm('Tem certeza que deseja excluir este vídeo?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash"></i>
                    Excluir
                </button>
            </form>
        </div>
    </div>

    <!-- Content -->
    <div class="content-section">
        <div class="details-grid">
            <!-- Video Display -->
            <div class="details-card full-width">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-video"></i>
                        Vídeo
                    </h3>
                </div>
                <div class="card-content">
                    <div class="video-display">
                        <video controls class="main-video" preload="metadata">
                            <source src="{{ $video->url }}" type="{{ $video->mime_type }}">
                            Seu navegador não suporta o elemento de vídeo.
                        </video>
                        <div class="video-actions">
                            <a href="{{ $video->url }}" target="_blank" class="btn btn-outline">
                                <i class="fas fa-external-link-alt"></i>
                                Abrir em Nova Aba
                            </a>
                            <button type="button" class="btn btn-outline" onclick="copyToClipboard('{{ $video->url }}', this)">
                            <i class="fas fa-copy"></i> Copiar URL
                        </button>
                            @if($video->thumbnail_url)
                                <button type="button" class="btn btn-outline" onclick="copyToClipboard('{{ $video->thumbnail_url }}', this)">                                    <i class="fas fa-image"></i>
                                    Copiar URL da Thumbnail
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thumbnail Display -->
            @if($video->thumbnail_url)
            <div class="details-card full-width">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-image"></i>
                        Thumbnail
                    </h3>
                </div>
                <div class="card-content">
                    <div class="thumbnail-display">
                        <img src="{{ $video->thumbnail_url }}" alt="Thumbnail do vídeo" class="thumbnail-image">
                    </div>
                </div>
            </div>
            @endif

            <!-- Basic Information -->
            <div class="details-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-info-circle"></i>
                        Informações Básicas
                    </h3>
                </div>
                <div class="card-content">
                    <div class="info-grid">
                        <div class="info-item">
                            <label class="info-label">Título:</label>
                            <span class="info-value">{{ $video->title }}</span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Slug:</label>
                            <span class="info-value">{{ $video->slug }}</span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Idioma:</label>
                            <span class="badge badge-{{ $video->language === 'pt' ? 'success' : ($video->language === 'en' ? 'primary' : 'warning') }}">
                                {{ strtoupper($video->language) }}
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Status:</label>
                            <span class="status-badge status-{{ $video->is_active ? 'active' : 'inactive' }}">
                                {{ $video->is_active ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Ordem:</label>
                            <span class="badge badge-outline">{{ $video->sort_order }}</span>
                        </div>

                        @if($video->duration)
                        <div class="info-item">
                            <label class="info-label">Duração:</label>
                            <span class="badge badge-info">{{ $video->formatted_duration }}</span>
                        </div>
                        @endif
                    </div>
                    
                    @if($video->description)
                        <div class="info-item full-width">
                            <label class="info-label">Descrição:</label>
                            <div class="info-description">{{ $video->description }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- File Information -->
            <div class="details-card">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-file"></i>
                        Informações do Arquivo
                    </h3>
                </div>
                <div class="card-content">
                    <div class="info-grid">
                        <div class="info-item">
                            <label class="info-label">Nome Original:</label>
                            <span class="info-value">{{ $video->original_filename }}</span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Nome do Arquivo:</label>
                            <span class="info-value">{{ $video->filename }}</span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Tipo MIME:</label>
                            <span class="badge badge-info">{{ $video->mime_type }}</span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Tamanho:</label>
                            <span class="badge badge-secondary">{{ $video->formatted_file_size }}</span>
                        </div>
                        
                        @if($video->width && $video->height)
                            <div class="info-item">
                                <label class="info-label">Dimensões:</label>
                                <span class="badge badge-info">{{ $video->dimensions }}</span>
                            </div>
                        @endif
                        
                        <div class="info-item">
                            <label class="info-label">Caminho:</label>
                            <span class="info-value text-small">{{ $video->path }}</span>
                        </div>

                        @if($video->thumbnail_path)
                        <div class="info-item">
                            <label class="info-label">Caminho da Thumbnail:</label>
                            <span class="info-value text-small">{{ $video->thumbnail_path }}</span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Timestamps -->
            <div class="details-card full-width">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-clock"></i>
                        Datas
                    </h3>
                </div>
                <div class="card-content">
                    <div class="info-grid">
                        <div class="info-item">
                            <label class="info-label">Criado em:</label>
                            <span class="info-value">{{ $video->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Atualizado em:</label>
                            <span class="info-value">{{ $video->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.video-display {
    text-align: center;
}

.main-video {
    max-width: 100%;
    max-height: 500px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
}

.thumbnail-display {
    text-align: center;
}

.thumbnail-image {
    max-width: 300px;
    max-height: 200px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.video-actions {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
    flex-wrap: wrap;
}

.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
}

.details-card {
    background: white;
    border: 1px solid #e9ecef;
    border-radius: 8px;
    overflow: hidden;
}

.details-card.full-width {
    grid-column: 1 / -1;
}

.card-header {
    background: #f8f9fa;
    padding: 1rem;
    border-bottom: 1px solid #e9ecef;
}

.card-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
}

.card-title i {
    margin-right: 0.5rem;
    color: #6c757d;
}

.card-content {
    padding: 1.5rem;
}

.info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.info-item {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.info-item.full-width {
    grid-column: 1 / -1;
}

.info-label {
    font-weight: 600;
    color: #495057;
    font-size: 0.875rem;
}

.info-value {
    color: #6c757d;
}

.info-description {
    color: #6c757d;
    line-height: 1.5;
    padding: 0.75rem;
    background: #f8f9fa;
    border-radius: 4px;
    border-left: 3px solid #007bff;
}

.text-small {
    font-size: 0.875rem;
    word-break: break-all;
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
}

.status-active {
    background-color: #d4edda;
    color: #155724;
}

.status-inactive {
    background-color: #f8d7da;
    color: #721c24;
}

.badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    font-size: 0.75rem;
    font-weight: 600;
    border-radius: 4px;
}

.badge-success {
    background-color: #d4edda;
    color: #155724;
}

.badge-primary {
    background-color: #cce7ff;
    color: #004085;
}

.badge-warning {
    background-color: #fff3cd;
    color: #856404;
}

.badge-info {
    background-color: #d1ecf1;
    color: #0c5460;
}

.badge-secondary {
    background-color: #e2e3e5;
    color: #383d41;
}

.badge-outline {
    background-color: transparent;
    border: 1px solid #6c757d;
    color: #6c757d;
}

@media (max-width: 768px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .video-actions {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<script>
function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        const btn = button;
        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-check"></i> Copiado!';
        btn.classList.add('btn-success');
        btn.classList.remove('btn-outline');
        
        setTimeout(function() {
            btn.innerHTML = originalText;
            btn.classList.remove('btn-success');
            btn.classList.add('btn-outline');
        }, 2000);
    }).catch(function(err) {
        console.error('Erro ao copiar: ', err);
        alert('Erro ao copiar URL');
    });
}
</script>
@endsection