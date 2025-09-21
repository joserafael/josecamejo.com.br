@extends('layouts.admin')

@section('title', 'Gerenciar Usuários - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-users"></i>
                Gerenciar Usuários
            </h1>
            <p class="page-description">Lista de todos os usuários do sistema</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Novo Usuário
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label for="search" class="form-label">Buscar:</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" 
                           placeholder="Nome ou email..." class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="type" class="form-label">Tipo:</label>
                    <select id="type" name="type" class="form-select">
                        <option value="">Todos</option>
                        <option value="admin" {{ request('type') === 'admin' ? 'selected' : '' }}>Administradores</option>
                        <option value="user" {{ request('type') === 'user' ? 'selected' : '' }}>Usuários</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search"></i>
                            Filtrar
                        </button>
                        <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i>
                            Limpar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Users Table -->
    <div class="users-table-card">
        <div class="table-header">
            <div class="table-title">
                <h3>
                    <i class="fas fa-users"></i>
                    Usuários ({{ $users->total() }})
                </h3>
                <span class="table-subtitle">Gerencie todos os usuários do sistema</span>
            </div>
            <div class="table-actions">
                <button class="btn btn-outline btn-sm" onclick="toggleTableView()">
                    <i class="fas fa-th-list"></i>
                    Alternar Visualização
                </button>
            </div>
        </div>
        
        @if($users->count() > 0)
            <div class="table-responsive">
                <table class="users-table enhanced-table">
                    <thead>
                        <tr>
                            <th class="sortable" data-sort="id">
                                <div class="th-content">
                                    <span>ID</span>
                                    <i class="fas fa-sort sort-icon"></i>
                                </div>
                            </th>
                            <th class="sortable" data-sort="name">
                                <div class="th-content">
                                    <span>Nome</span>
                                    <i class="fas fa-sort sort-icon"></i>
                                </div>
                            </th>
                            <th class="sortable" data-sort="email">
                                <div class="th-content">
                                    <span>Email</span>
                                    <i class="fas fa-sort sort-icon"></i>
                                </div>
                            </th>
                            <th class="sortable" data-sort="type">
                                <div class="th-content">
                                    <span>Tipo</span>
                                    <i class="fas fa-sort sort-icon"></i>
                                </div>
                            </th>
                            <th class="sortable" data-sort="created_at">
                                <div class="th-content">
                                    <span>Criado em</span>
                                    <i class="fas fa-sort sort-icon"></i>
                                </div>
                            </th>
                            <th class="actions-column">
                                <div class="th-content">
                                    <span>Ações</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr class="table-row" data-user-id="{{ $user->id }}">
                                <td class="id-column">
                                    <span class="user-id">#{{ str_pad($user->id, 3, '0', STR_PAD_LEFT) }}</span>
                                </td>
                                <td class="user-column">
                                    <div class="user-info">
                                        <div class="user-avatar {{ $user->is_admin ? 'admin-avatar' : 'user-avatar' }}">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div class="user-details">
                                            <span class="user-name">{{ $user->name }}</span>
                                            <span class="user-meta">{{ $user->email }}</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="email-column">
                                    <a href="mailto:{{ $user->email }}" class="email-link">
                                        {{ $user->email }}
                                    </a>
                                </td>
                                <td class="type-column">
                                    <div class="user-type-wrapper">
                                        <span class="badge {{ $user->is_admin ? 'badge-admin' : 'badge-user' }}">
                                            <i class="fas {{ $user->is_admin ? 'fa-crown' : 'fa-user' }}"></i>
                                            {{ $user->is_admin ? 'Administrador' : 'Usuário' }}
                                        </span>
                                        @if($user->is_admin)
                                            <span class="admin-indicator" title="Usuário Administrador">
                                                <i class="fas fa-shield-alt"></i>
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="date-column">
                                    <div class="date-info">
                                        <span class="date-primary">{{ $user->created_at->format('d/m/Y') }}</span>
                                        <span class="date-secondary">{{ $user->created_at->format('H:i') }}</span>
                                        <span class="date-relative" title="{{ $user->created_at->format('d/m/Y H:i:s') }}">
                                            {{ $user->created_at->diffForHumans() }}
                                        </span>
                                    </div>
                                </td>
                                <td class="actions-column">
                                    <div class="action-buttons">
                                        <div class="btn-group">
                                            <a href="{{ route('admin.users.show', $user) }}" 
                                               class="btn btn-sm btn-info action-btn" 
                                               title="Visualizar Usuário"
                                               data-tooltip="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.users.edit', $user) }}" 
                                               class="btn btn-sm btn-warning action-btn" 
                                               title="Editar Usuário"
                                               data-tooltip="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <a href="{{ route('admin.users.change-password', $user) }}" 
                                               class="btn btn-sm btn-secondary action-btn" 
                                               title="Alterar Senha"
                                               data-tooltip="Senha">
                                                <i class="fas fa-key"></i>
                                            </a>
                                            @if($user->id !== auth()->id())
                                                <button type="button" 
                                                        class="btn btn-sm btn-danger action-btn delete-user-btn" 
                                                        title="Deletar Usuário"
                                                        data-tooltip="Deletar"
                                                        data-user-id="{{ $user->id }}"
                                                        data-user-name="{{ $user->name }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @else
                                                <span class="btn btn-sm btn-disabled action-btn" 
                                                      title="Não é possível deletar seu próprio usuário"
                                                      data-tooltip="Você">
                                                    <i class="fas fa-user-shield"></i>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $users->links() }}
            </div>
        @else
            <div class="empty-state">
                <i class="fas fa-users"></i>
                <h3>Nenhum usuário encontrado</h3>
                <p>Não há usuários que correspondam aos filtros aplicados.</p>
                <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Criar Primeiro Usuário
                </a>
            </div>
        @endif
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-error">
        {{ session('error') }}
    </div>
@endif
@endsection