@extends('layouts.admin')

@section('title', 'Alterar Senha - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-key"></i>
                Alterar Senha
            </h1>
            <p class="page-description">Definir nova senha para {{ $user->name }}</p>
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

    <div class="content-grid">
        <!-- User Info -->
        <div class="info-card">
            <div class="card-header">
                <h3>
                    <i class="fas fa-user"></i>
                    Usuário
                </h3>
            </div>
            <div class="card-body">
                <div class="user-profile-mini">
                    <div class="user-avatar">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="user-info">
                        <h4>{{ $user->name }}</h4>
                        <p>{{ $user->email }}</p>
                        <span class="badge {{ $user->is_admin ? 'badge-admin' : 'badge-user' }}">
                            {{ $user->is_admin ? 'Administrador' : 'Usuário' }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Password Form -->
        <div class="users-form-card">
            <div class="card-header">
                <h3>
                    <i class="fas fa-lock"></i>
                    Nova Senha
                </h3>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.users.update-password', $user) }}" class="users-password-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="form-section">
                        <div class="security-notice">
                            <i class="fas fa-shield-alt"></i>
                            <div>
                                <h4>Segurança da Senha</h4>
                                <p>A nova senha deve ter pelo menos 8 caracteres e ser segura.</p>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="password" class="form-label required">Nova Senha</label>
                                <div class="password-input-group">
                                    <input type="password" id="password" name="password" 
                                           class="form-input @error('password') error @enderror" 
                                           placeholder="Digite a nova senha" required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                @error('password')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                                <div class="password-strength">
                                    <div class="strength-bar">
                                        <div class="strength-fill"></div>
                                    </div>
                                    <span class="strength-text">Digite uma senha</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="password_confirmation" class="form-label required">Confirmar Nova Senha</label>
                                <div class="password-input-group">
                                    <input type="password" id="password_confirmation" name="password_confirmation" 
                                           class="form-input" placeholder="Confirme a nova senha" required>
                                    <button type="button" class="password-toggle" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="form-help">Digite a mesma senha para confirmação</small>
                            </div>
                        </div>

                        <div class="password-requirements">
                            <h5>Requisitos da senha:</h5>
                            <ul>
                                <li id="req-length">Pelo menos 8 caracteres</li>
                                <li id="req-letter">Pelo menos uma letra</li>
                                <li id="req-number">Pelo menos um número</li>
                                <li id="req-special">Pelo menos um caractere especial</li>
                            </ul>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Alterar Senha
                        </button>
                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-outline">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-error">
        <strong>Erro ao alterar senha:</strong>
        <ul>
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<script>
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const button = field.nextElementSibling;
    const icon = button.querySelector('i');
    
    if (field.type === 'password') {
        field.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        field.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Password strength checker
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthBar = document.querySelector('.strength-fill');
    const strengthText = document.querySelector('.strength-text');
    
    let strength = 0;
    let text = 'Muito fraca';
    
    // Check requirements
    const hasLength = password.length >= 8;
    const hasLetter = /[a-zA-Z]/.test(password);
    const hasNumber = /\d/.test(password);
    const hasSpecial = /[!@#$%^&*(),.?":{}|<>]/.test(password);
    
    // Update requirement indicators
    document.getElementById('req-length').className = hasLength ? 'valid' : '';
    document.getElementById('req-letter').className = hasLetter ? 'valid' : '';
    document.getElementById('req-number').className = hasNumber ? 'valid' : '';
    document.getElementById('req-special').className = hasSpecial ? 'valid' : '';
    
    // Calculate strength
    if (hasLength) strength += 25;
    if (hasLetter) strength += 25;
    if (hasNumber) strength += 25;
    if (hasSpecial) strength += 25;
    
    // Set text and color
    if (strength === 0) {
        text = 'Digite uma senha';
    } else if (strength <= 25) {
        text = 'Muito fraca';
    } else if (strength <= 50) {
        text = 'Fraca';
    } else if (strength <= 75) {
        text = 'Boa';
    } else {
        text = 'Forte';
    }
    
    strengthBar.style.width = strength + '%';
    strengthBar.className = 'strength-fill strength-' + Math.ceil(strength / 25);
    strengthText.textContent = text;
});
</script>
@endsection