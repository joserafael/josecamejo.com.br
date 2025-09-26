@extends('layouts.admin')

@section('title', 'Visualizar Tag - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-eye"></i>
                <span class="tag-badge" style="background-color: {{ $tag->color }}">
                    {{ $tag->name }}
                </span>
            </h1>
            <p class="page-description">Detalhes da tag do blog</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.blog-tags.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <a href="{{ route('admin.blog-tags.edit', $tag) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i>
                Editar
            </a>
        </div>
    </div>

    <div class="content-grid">
        <!-- Tag Details -->
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-info-circle"></i>
                    Informações da Tag
                </h3>
                <div class="status-badge {{ $tag->is_active ? 'active' : 'inactive' }}">
                    {{ $tag->is_active ? 'Ativa' : 'Inativa' }}
                </div>
            </div>
            
            <div class="card-content">
                <div class="details-grid">
                    <div class="detail-item">
                        <label class="detail-label">Slug:</label>
                        <code class="detail-value">{{ $tag->slug }}</code>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Cor:</label>
                        <div class="color-display">
                            <div class="color-swatch" style="background-color: {{ $tag->color }}"></div>
                            <code class="color-code">{{ $tag->color }}</code>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Criado em:</label>
                        <span class="detail-value">{{ $tag->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <label class="detail-label">Atualizado em:</label>
                        <span class="detail-value">{{ $tag->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tag Preview -->
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-eye"></i>
                    Prévia da Tag
                </h3>
            </div>
            
            <div class="card-content">
                <div class="tag-preview-section">
                    <p class="preview-description">Como a tag aparece no site:</p>
                    <div class="tag-examples">
                        <div class="example-item">
                            <span class="example-label">Tag normal:</span>
                            <span class="tag-badge" style="background-color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </span>
                        </div>
                        
                        <div class="example-item">
                            <span class="example-label">Tag pequena:</span>
                            <span class="tag-badge tag-sm" style="background-color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </span>
                        </div>
                        
                        <div class="example-item">
                            <span class="example-label">Tag grande:</span>
                            <span class="tag-badge tag-lg" style="background-color: {{ $tag->color }}">
                                {{ $tag->name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tag Content -->
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-tag"></i>
                    Conteúdo da Tag
                </h3>
            </div>
            
            <div class="card-content">
                <div class="content-grid">
                    <div class="content-item">
                        <label class="content-label">Nome:</label>
                        <span class="content-value">{{ $tag->name }}</span>
                    </div>
                    
                    <div class="content-item">
                        <label class="content-label">Idioma:</label>
                        <span class="content-value">
                            @switch($tag->language)
                                @case('pt')
                                    🇧🇷 Português
                                    @break
                                @case('en')
                                    🇺🇸 English
                                    @break
                                @case('es')
                                    🇪🇸 Español
                                    @break
                                @default
                                    {{ $tag->language }}
                            @endswitch
                        </span>
                    </div>
                </div>
            </div>
        </div>

        @if($tag->description)
        <!-- Tag Description -->
        <div class="details-card">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-align-left"></i>
                    Descrição
                </h3>
            </div>
            
            <div class="card-content">
                <div class="description-content">
                    {{ $tag->description }}
                </div>
            </div>
        </div>
        @endif

        <!-- Usage Statistics -->
        <div class="details-card full-width">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-chart-bar"></i>
                    Estatísticas de Uso
                </h3>
            </div>
            
            <div class="card-content">
                <div class="stats-grid">
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Posts Associados</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-eye"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Visualizações</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-calendar"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $tag->created_at->diffForHumans() }}</div>
                            <div class="stat-label">Criada</div>
                        </div>
                    </div>
                    
                    <div class="stat-item">
                        <div class="stat-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">{{ $tag->updated_at->diffForHumans() }}</div>
                            <div class="stat-label">Última Atualização</div>
                        </div>
                    </div>
                </div>
                
                <div class="usage-note">
                    <p><strong>Nota:</strong> As estatísticas de posts associados e visualizações serão implementadas quando o sistema de posts estiver completo.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
.content-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1.5rem;
    margin-top: 1.5rem;
}

.details-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    overflow: hidden;
}

.details-card.full-width {
    grid-column: 1 / -1;
}

.card-header {
    background: #f8f9fa;
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-title {
    margin: 0;
    font-size: 1.1rem;
    font-weight: 600;
    color: #495057;
}

.card-content {
    padding: 1.5rem;
}

.details-grid {
    display: grid;
    gap: 1rem;
}

.detail-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid #f1f3f4;
}

.detail-item:last-child {
    border-bottom: none;
}

.detail-label {
    font-weight: 600;
    color: #6c757d;
    margin: 0;
}

.detail-value {
    color: #495057;
}

.color-display {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.color-swatch {
    width: 24px;
    height: 24px;
    border-radius: 4px;
    border: 1px solid #dee2e6;
}

.color-code {
    background: #f8f9fa;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    font-size: 0.8rem;
    color: #495057;
}

.tag-preview-section {
    text-align: center;
}

.preview-description {
    margin: 0 0 1.5rem 0;
    color: #6c757d;
}

.tag-examples {
    display: flex;
    flex-direction: column;
    gap: 1rem;
    align-items: center;
}

.example-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    width: 100%;
    justify-content: space-between;
}

.example-label {
    font-weight: 600;
    color: #495057;
    min-width: 100px;
    text-align: left;
}

.tag-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
    color: white;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.tag-badge.tag-sm {
    padding: 0.125rem 0.5rem;
    font-size: 0.75rem;
}

.tag-badge.tag-lg {
    padding: 0.5rem 1rem;
    font-size: 1rem;
}

.page-title .tag-badge {
    margin-left: 0.5rem;
}

.multilingual-grid {
    display: grid;
    gap: 1rem;
}

.language-item {
    border: 1px solid #e9ecef;
    border-radius: 6px;
    overflow: hidden;
}

.language-header {
    background: #f8f9fa;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 600;
    color: #495057;
}

.language-content {
    padding: 1rem;
    color: #6c757d;
}

.language-content.description {
    white-space: pre-wrap;
    line-height: 1.5;
}

.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 20px;
    font-size: 0.875rem;
    font-weight: 500;
}

.status-badge.active {
    background: #d4edda;
    color: #155724;
}

.status-badge.inactive {
    background: #f8d7da;
    color: #721c24;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    background: #f8f9fa;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.stat-icon {
    width: 50px;
    height: 50px;
    background: #007bff;
    color: white;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #495057;
    margin-bottom: 0.25rem;
}

.stat-label {
    font-size: 0.875rem;
    color: #6c757d;
    font-weight: 500;
}

.usage-note {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 6px;
    padding: 1rem;
    color: #1976d2;
    font-size: 0.875rem;
}

.usage-note p {
    margin: 0;
}

.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 4px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    transition: all 0.15s ease-in-out;
}

.btn-primary {
    background: #007bff;
    color: white;
}

.btn-primary:hover {
    background: #0056b3;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
}

@media (max-width: 768px) {
    .content-grid {
        grid-template-columns: 1fr;
    }
    
    .detail-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .color-display {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.25rem;
    }
    
    .example-item {
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }
    
    .example-label {
        text-align: center;
        min-width: auto;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .stat-item {
        flex-direction: column;
        text-align: center;
    }
    
    .page-title {
        flex-direction: column;
        align-items: flex-start;
        gap: 0.5rem;
    }
    
    .page-title .tag-badge {
        margin-left: 0;
    }
}
</style>
@endsection