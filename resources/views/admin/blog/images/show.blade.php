@extends('layouts.admin')

@section('title', 'Imagem: ' . $image->title . ' - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-image"></i>
                {{ $image->title }}
            </h1>
            <p class="page-description">Detalhes da imagem do blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-images.edit', $image) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Editar
            </a>
            <a href="{{ route('admin.blog-images.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <form method="POST" action="{{ route('admin.blog-images.destroy', $image) }}" 
                  style="display: inline;" 
                  onsubmit="return confirm('Tem certeza que deseja excluir esta imagem?')">
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
            <!-- Image Display -->
            <div class="details-card full-width">
                <div class="card-header">
                    <h3 class="card-title">
                        <i class="fas fa-image"></i>
                        Imagem
                    </h3>
                </div>
                <div class="card-content">
                    <div class="image-display">
                        <img src="{{ $image->url }}" alt="{{ $image->alt_text }}" class="main-image">
                        <div class="image-actions">
                            <a href="{{ $image->url }}" target="_blank" class="btn btn-outline">
                                <i class="fas fa-external-link-alt"></i>
                                Abrir em Nova Aba
                            </a>
                            <button type="button" class="btn btn-outline" onclick="copyToClipboard('{{ $image->url }}', this)">
                                <i class="fas fa-copy"></i>
                                Copiar URL
                            </button>
                        </div>
                    </div>
                </div>
            </div>

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
                            <span class="info-value">{{ $image->title }}</span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Slug:</label>
                            <span class="info-value">{{ $image->slug }}</span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Texto Alternativo:</label>
                            <span class="info-value">{{ $image->alt_text ?: 'Não definido' }}</span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Idioma:</label>
                            <span class="badge badge-{{ $image->language === 'pt' ? 'success' : ($image->language === 'en' ? 'primary' : 'warning') }}">
                                {{ strtoupper($image->language) }}
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Status:</label>
                            <span class="status-badge status-{{ $image->is_active ? 'active' : 'inactive' }}">
                                {{ $image->is_active ? 'Ativa' : 'Inativa' }}
                            </span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Ordem:</label>
                            <span class="badge badge-outline">{{ $image->sort_order }}</span>
                        </div>
                    </div>
                    
                    @if($image->description)
                        <div class="info-item full-width">
                            <label class="info-label">Descrição:</label>
                            <div class="info-description">{{ $image->description }}</div>
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
                            <span class="info-value">{{ $image->original_filename }}</span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Nome do Arquivo:</label>
                            <span class="info-value">{{ $image->filename }}</span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Tipo MIME:</label>
                            <span class="badge badge-info">{{ $image->mime_type }}</span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Tamanho:</label>
                            <span class="badge badge-secondary">{{ $image->formatted_file_size }}</span>
                        </div>
                        
                        @if($image->width && $image->height)
                            <div class="info-item">
                                <label class="info-label">Dimensões:</label>
                                <span class="badge badge-info">{{ $image->dimensions }}</span>
                            </div>
                        @endif
                        
                        <div class="info-item">
                            <label class="info-label">Caminho:</label>
                            <span class="info-value text-small">{{ $image->path }}</span>
                        </div>
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
                            <span class="info-value">{{ $image->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                        
                        <div class="info-item">
                            <label class="info-label">Atualizado em:</label>
                            <span class="info-value">{{ $image->updated_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.image-display {
    text-align: center;
}

.main-image {
    max-width: 100%;
    max-height: 500px;
    border-radius: 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    margin-bottom: 1rem;
}

.image-actions {
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

@media (max-width: 768px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
    
    .info-grid {
        grid-template-columns: 1fr;
    }
    
    .image-actions {
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