@extends('layouts.admin')

@section('title', 'Editar Usuário - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-user-edit"></i>
                Editar Usuário
            </h1>
            <p class="page-description">Editar informações de {{ $user->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info">
                <i class="fas fa-eye"></i>
                Visualizar
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="users-form-card">
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="users-form">
            @csrf
            @method('PUT')
            
            <div class="form-section">
                <h3 class="section-title">Informações Básicas</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label required">Nome Completo</label>
                        <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" 
                               class="form-input @error('name') error @enderror" 
                               placeholder="Digite o nome completo" required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email" class="form-label required">Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" 
                               class="form-input @error('email') error @enderror" 
                               placeholder="Digite o email" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Permissões</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="is_admin" value="1" 
                                       {{ old('is_admin', $user->is_admin) ? 'checked' : '' }} 
                                       class="form-checkbox"
                                       {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-text">
                                    <strong>Administrador</strong>
                                    <small>Usuário terá acesso total ao painel administrativo</small>
                                    @if($user->id === auth()->id())
                                        <small class="text-warning">Você não pode alterar suas próprias permissões</small>
                                    @endif
                                </span>
                            </label>
                        </div>
                        @error('is_admin')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Informações do Sistema</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Criado em</label>
                        <input type="text" value="{{ $user->created_at->format('d/m/Y H:i:s') }}" 
                               class="form-input" readonly>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Última atualização</label>
                        <input type="text" value="{{ $user->updated_at->format('d/m/Y H:i:s') }}" 
                               class="form-input" readonly>
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Salvar Alterações
                </button>
                <a href="{{ route('admin.users.change-password', $user) }}" class="btn btn-warning">
                    <i class="fas fa-key"></i>
                    Alterar Senha
                </a>
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline">
                    <i class="fas fa-times"></i>
                    Cancelar
                </a>
            </div>
        </form>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-error">
        <strong>Erro ao atualizar usuário:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endsection