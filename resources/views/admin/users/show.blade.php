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
            <p class="page-description">Detalhes de {{ $user->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i>
                Editar
            </a>
            <a href="{{ route('admin.users.change-password', $user) }}" class="btn btn-info">
                <i class="fas fa-key"></i>
                Alterar Senha
            </a>
        </div>
    </div>

    <div class="users-content-grid">
        <!-- User Info Card -->
        <div class="users-info-card">
            <div class="card-header">
                <h3>
                    <i class="fas fa-user"></i>
                    Informações do Usuário
                </h3>
            </div>
            <div class="card-body">
                <div class="user-profile">
                    <div class="user-avatar-large">
                        {{ strtoupper(substr($user->name, 0, 2)) }}
                    </div>
                    <div class="user-details">
                        <h2 class="user-name">{{ $user->name }}</h2>
                        <p class="user-email">{{ $user->email }}</p>
                        <span class="badge {{ $user->is_admin ? 'badge-admin' : 'badge-user' }}">
                            {{ $user->is_admin ? 'Administrador' : 'Usuário' }}
                        </span>
                    </div>
                </div>

                <div class="info-grid">
                    <div class="info-item">
                        <label>ID do Usuário</label>
                        <span>{{ $user->id }}</span>
                    </div>
                    <div class="info-item">
                        <label>Nome Completo</label>
                        <span>{{ $user->name }}</span>
                    </div>
                    <div class="info-item">
                        <label>Email</label>
                        <span>{{ $user->email }}</span>
                    </div>
                    <div class="info-item">
                        <label>Tipo de Usuário</label>
                        <span>
                            <span class="badge {{ $user->is_admin ? 'badge-admin' : 'badge-user' }}">
                                {{ $user->is_admin ? 'Administrador' : 'Usuário' }}
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <label>Criado em</label>
                        <span>{{ $user->created_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                    <div class="info-item">
                        <label>Última atualização</label>
                        <span>{{ $user->updated_at->format('d/m/Y H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Card -->
        <div class="actions-card">
            <div class="card-header">
                <h3>
                    <i class="fas fa-cogs"></i>
                    Ações Disponíveis
                </h3>
            </div>
            <div class="card-body">
                <div class="action-list">
                    <a href="{{ route('admin.users.edit', $user) }}" class="action-item">
                        <div class="action-icon edit">
                            <i class="fas fa-edit"></i>
                        </div>
                        <div class="action-content">
                            <h4>Editar Usuário</h4>
                            <p>Alterar informações básicas e permissões</p>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>

                    <a href="{{ route('admin.users.change-password', $user) }}" class="action-item">
                        <div class="action-icon password">
                            <i class="fas fa-key"></i>
                        </div>
                        <div class="action-content">
                            <h4>Alterar Senha</h4>
                            <p>Definir uma nova senha para o usuário</p>
                        </div>
                        <i class="fas fa-chevron-right"></i>
                    </a>

                    @if($user->id !== auth()->id())
                        <div class="action-item danger" onclick="confirmDelete()">
                            <div class="action-icon delete">
                                <i class="fas fa-trash"></i>
                            </div>
                            <div class="action-content">
                                <h4>Deletar Usuário</h4>
                                <p>Remover permanentemente do sistema</p>
                            </div>
                            <i class="fas fa-chevron-right"></i>
                        </div>
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
            if (confirm('Tem certeza que deseja deletar este usuário?\n\nEsta ação não pode ser desfeita.')) {
                document.getElementById('deleteForm').submit();
            }
        }
    </script>
@endif
@endsection