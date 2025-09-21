@extends('layouts.admin')

@section('title', 'Visualizar Usuário - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-user"></i>
                Visualizar Usuário
            </h1>
            <p class="page-description">Detalhes completos de {{ $user->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-secondary">
                <i class="fas fa-edit"></i>
                Editar
            </a>
            <a href="{{ route('admin.users.change-password', $user) }}" class="btn btn-primary">
                <i class="fas fa-key"></i>
                Alterar Senha
            </a>
        </div>
    </div>

    <!-- User Profile Card -->
    <div class="detail-card">
        <div class="detail-card-header">
            <div class="detail-card-title">
                <i class="fas fa-user-circle"></i>
                <span>Perfil do Usuário</span>
            </div>
            <div class="detail-card-status">
                <span class="status-badge {{ $user->is_admin ? 'status-admin' : 'status-user' }}">
                    <i class="fas {{ $user->is_admin ? 'fa-crown' : 'fa-user' }}"></i>
                    {{ $user->is_admin ? 'Administrador' : 'Usuário' }}
                </span>
            </div>
        </div>
        
        <div class="detail-card-body">
            <div class="profile-section">
                <div class="profile-avatar">
                    <div class="avatar-circle {{ $user->is_admin ? 'avatar-admin' : 'avatar-user' }}">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                </div>
                <div class="profile-info">
                    <h2 class="profile-name">{{ $user->name }}</h2>
                    <p class="profile-email">
                        <i class="fas fa-envelope"></i>
                        {{ $user->email }}
                    </p>
                    <div class="profile-meta">
                        <span class="meta-item">
                            <i class="fas fa-hashtag"></i>
                            ID: {{ $user->id }}
                        </span>
                        <span class="meta-item">
                            <i class="fas fa-calendar-plus"></i>
                            Criado em {{ $user->created_at->format('d/m/Y') }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="details-grid">
        <!-- Information Card -->
        <div class="detail-card">
            <div class="detail-card-header">
                <div class="detail-card-title">
                    <i class="fas fa-info-circle"></i>
                    <span>Informações Detalhadas</span>
                </div>
            </div>
            <div class="detail-card-body">
                <div class="info-list">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-user"></i>
                            Nome Completo
                        </div>
                        <div class="info-value">{{ $user->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-envelope"></i>
                            Endereço de Email
                        </div>
                        <div class="info-value">{{ $user->email }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-shield-alt"></i>
                            Nível de Acesso
                        </div>
                        <div class="info-value">
                            <span class="access-level {{ $user->is_admin ? 'access-admin' : 'access-user' }}">
                                {{ $user->is_admin ? 'Administrador do Sistema' : 'Usuário Padrão' }}
                            </span>
                        </div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-calendar-alt"></i>
                            Data de Criação
                        </div>
                        <div class="info-value">{{ $user->created_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-clock"></i>
                            Última Atualização
                        </div>
                        <div class="info-value">{{ $user->updated_at->format('d/m/Y H:i:s') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="detail-card">
            <div class="detail-card-header">
                <div class="detail-card-title">
                    <i class="fas fa-tools"></i>
                    <span>Ações Disponíveis</span>
                </div>
            </div>
            <div class="detail-card-body">
                <div class="actions-list">
                    <a href="{{ route('admin.users.edit', $user) }}" class="action-button action-edit">
                        <div class="action-icon">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="action-content">
                            <div class="action-title">Editar Usuário</div>
                            <div class="action-description">Alterar informações e permissões</div>
                        </div>
                        <div class="action-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>

                    <a href="{{ route('admin.users.change-password', $user) }}" class="action-button action-password">
                        <div class="action-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="action-content">
                            <div class="action-title">Alterar Senha</div>
                            <div class="action-description">Definir nova senha de acesso</div>
                        </div>
                        <div class="action-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </a>

                    @if($user->id !== auth()->id())
                        <button type="button" class="action-button action-delete" onclick="confirmDelete()">
                            <div class="action-icon">
                                <i class="fas fa-trash-alt"></i>
                            </div>
                            <div class="action-content">
                                <div class="action-title">Excluir Usuário</div>
                                <div class="action-description">Remover permanentemente</div>
                            </div>
                            <div class="action-arrow">
                                <i class="fas fa-chevron-right"></i>
                            </div>
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@if($user->id !== auth()->id())
    <!-- Delete Form (Hidden) -->
    <form id="deleteForm" method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <script>
        function confirmDelete() {
            if (confirm('Tem certeza que deseja excluir este usuário?\n\nEsta ação não pode ser desfeita.')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@endif

<style>
/* User Show Page Styles */
.detail-card {
    background: #ffffff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    border: 1px solid #e5e7eb;
    margin-bottom: 24px;
    overflow: hidden;
}

.detail-card-header {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    border-bottom: 1px solid #e5e7eb;
    padding: 20px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.detail-card-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-weight: 600;
    font-size: 18px;
    color: #1f2937;
}

.detail-card-title i {
    color: #6366f1;
    font-size: 20px;
}

.detail-card-status {
    display: flex;
    align-items: center;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
}

.status-admin {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    color: #92400e;
    border: 1px solid #f59e0b;
}

.status-user {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    color: #1e40af;
    border: 1px solid #3b82f6;
}

.detail-card-body {
    padding: 24px;
}

/* Profile Section */
.profile-section {
    display: flex;
    align-items: center;
    gap: 24px;
    margin-bottom: 32px;
}

.profile-avatar {
    flex-shrink: 0;
}

.avatar-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 28px;
    font-weight: 700;
    color: white;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.1);
}

.avatar-admin {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
    border: 3px solid #fbbf24;
}

.avatar-user {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
    border: 3px solid #60a5fa;
}

.profile-info {
    flex: 1;
}

.profile-name {
    font-size: 28px;
    font-weight: 700;
    color: #111827;
    margin: 0 0 8px 0;
}

.profile-email {
    font-size: 16px;
    color: #6b7280;
    margin: 0 0 16px 0;
    display: flex;
    align-items: center;
    gap: 8px;
}

.profile-meta {
    display: flex;
    gap: 24px;
    flex-wrap: wrap;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: #6b7280;
}

.meta-item i {
    color: #9ca3af;
}

/* Details Grid */
.details-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 24px;
}

@media (max-width: 768px) {
    .details-grid {
        grid-template-columns: 1fr;
    }
}

/* Info List */
.info-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.info-row {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding: 16px;
    background: #f9fafb;
    border-radius: 8px;
    border-left: 4px solid #6366f1;
}

.info-label {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    font-weight: 500;
    color: #374151;
}

.info-label i {
    color: #6366f1;
    width: 16px;
}

.info-value {
    font-size: 16px;
    color: #111827;
    font-weight: 500;
}

.access-level {
    display: inline-flex;
    align-items: center;
    padding: 4px 12px;
    border-radius: 16px;
    font-size: 14px;
    font-weight: 500;
}

.access-admin {
    background: #fef3c7;
    color: #92400e;
}

.access-user {
    background: #dbeafe;
    color: #1e40af;
}

/* Actions List */
.actions-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.action-button {
    display: flex;
    align-items: center;
    gap: 16px;
    padding: 16px;
    background: #ffffff;
    border: 2px solid #e5e7eb;
    border-radius: 12px;
    text-decoration: none;
    color: inherit;
    transition: all 0.2s ease;
    cursor: pointer;
    width: 100%;
}

.action-button:hover {
    border-color: #6366f1;
    background: #f8fafc;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.15);
}

.action-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    color: white;
    flex-shrink: 0;
}

.action-edit .action-icon {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%);
}

.action-password .action-icon {
    background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
}

.action-delete .action-icon {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.action-content {
    flex: 1;
    text-align: left;
}

.action-title {
    font-size: 16px;
    font-weight: 600;
    color: #111827;
    margin-bottom: 4px;
}

.action-description {
    font-size: 14px;
    color: #6b7280;
}

.action-arrow {
    color: #9ca3af;
    font-size: 16px;
    flex-shrink: 0;
}

.action-delete:hover {
    border-color: #ef4444;
    background: #fef2f2;
}

.action-delete:hover .action-arrow {
    color: #ef4444;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .profile-section {
        flex-direction: column;
        text-align: center;
        gap: 16px;
    }
    
    .profile-meta {
        justify-content: center;
    }
    
    .detail-card-header {
        flex-direction: column;
        gap: 12px;
        text-align: center;
    }
}
</style>
@endsection