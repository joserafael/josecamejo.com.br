@extends('layouts.admin')

@section('title', 'Novo Usuário - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-user-plus"></i>
                Novo Usuário
            </h1>
            <p class="page-description">Criar um novo usuário no sistema</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Form Card -->
    <div class="users-form-card">
        <form method="POST" action="{{ route('admin.users.store') }}" class="users-form">
            @csrf
            
            <div class="form-section">
                <h3 class="section-title">Informações Básicas</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label required">Nome Completo</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" 
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
                        <input type="email" id="email" name="email" value="{{ old('email') }}" 
                               class="form-input @error('email') error @enderror" 
                               placeholder="Digite o email" required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h3 class="section-title">Senha</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="password" class="form-label required">Senha</label>
                        <input type="password" id="password" name="password" 
                               class="form-input @error('password') error @enderror" 
                               placeholder="Digite a senha" required>
                        @error('password')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                        <small class="form-help">A senha deve ter pelo menos 8 caracteres</small>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="password_confirmation" class="form-label required">Confirmar Senha</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" 
                               class="form-input" placeholder="Confirme a senha" required>
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
                                       {{ old('is_admin') ? 'checked' : '' }} class="form-checkbox">
                                <span class="checkbox-custom"></span>
                                <span class="checkbox-text">
                                    <strong>Administrador</strong>
                                    <small>Usuário terá acesso total ao painel administrativo</small>
                                </span>
                            </label>
                        </div>
                        @error('is_admin')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Criar Usuário
                </button>
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
        <strong>Erro ao criar usuário:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
@endsection