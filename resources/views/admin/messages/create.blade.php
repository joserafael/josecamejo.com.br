@extends('layouts.admin')

@section('title', 'Nova Mensagem - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-plus"></i>
                Nova Mensagem
            </h1>
            <p class="page-description">Criar uma nova mensagem manualmente</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.messages.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Create Form -->
    <div class="form-card">
        <form method="POST" action="{{ route('admin.messages.store') }}">
            @csrf
            
            <!-- Sender Information -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-user"></i>
                    Informações do Remetente
                </h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label required">Nome:</label>
                        <input type="text" id="name" name="name" 
                               value="{{ old('name') }}"
                               class="form-input @error('name') error @enderror" 
                               placeholder="Nome completo do remetente"
                               required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label required">Email:</label>
                        <input type="email" id="email" name="email" 
                               value="{{ old('email') }}"
                               class="form-input @error('email') error @enderror" 
                               placeholder="email@exemplo.com"
                               required>
                        @error('email')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="phone" class="form-label">Telefone:</label>
                        <input type="text" id="phone" name="phone" 
                               value="{{ old('phone') }}"
                               class="form-input @error('phone') error @enderror"
                               placeholder="(11) 99999-9999">
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="company" class="form-label">Empresa:</label>
                        <input type="text" id="company" name="company" 
                               value="{{ old('company') }}"
                               class="form-input @error('company') error @enderror"
                               placeholder="Nome da empresa (opcional)">
                        @error('company')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Message Content -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-envelope"></i>
                    Conteúdo da Mensagem
                </h3>
                <div class="form-group">
                    <label for="subject" class="form-label required">Assunto:</label>
                    <input type="text" id="subject" name="subject" 
                           value="{{ old('subject') }}"
                           class="form-input @error('subject') error @enderror" 
                           placeholder="Assunto da mensagem"
                           required>
                    @error('subject')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="message" class="form-label required">Mensagem:</label>
                    <textarea id="message" name="message" rows="6" 
                              class="form-textarea @error('message') error @enderror" 
                              placeholder="Digite o conteúdo da mensagem..."
                              required>{{ old('message') }}</textarea>
                    @error('message')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-help">
                        Digite o conteúdo completo da mensagem. Quebras de linha serão preservadas.
                    </small>
                </div>
            </div>

            <!-- Message Status -->
            <div class="form-section">
                <h3 class="section-title">
                    <i class="fas fa-cog"></i>
                    Status da Mensagem
                </h3>
                <div class="form-row">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_read" value="1" 
                                   {{ old('is_read') ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            Marcar como lida
                        </label>
                        <small class="form-help">
                            Se marcada, a mensagem será criada como já lida.
                        </small>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_replied" value="1" 
                                   {{ old('is_replied') ? 'checked' : '' }}
                                   onchange="toggleReplyField()">
                            <span class="checkmark"></span>
                            Marcar como respondida
                        </label>
                        <small class="form-help">
                            Se marcada, você pode adicionar uma resposta abaixo.
                        </small>
                    </div>
                </div>
            </div>

            <!-- Admin Reply (Hidden by default) -->
            <div id="replySection" class="form-section" style="display: none;">
                <h3 class="section-title">
                    <i class="fas fa-reply"></i>
                    Resposta do Administrador
                </h3>
                <div class="form-group">
                    <label for="admin_reply" class="form-label">Resposta:</label>
                    <textarea id="admin_reply" name="admin_reply" rows="4" 
                              class="form-textarea @error('admin_reply') error @enderror"
                              placeholder="Digite a resposta do administrador...">{{ old('admin_reply') }}</textarea>
                    @error('admin_reply')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-help">
                        Esta resposta será salva junto com a mensagem.
                    </small>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.messages.index') }}" class="btn btn-outline">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Criar Mensagem
                </button>
            </div>
        </form>
    </div>

    <!-- Help Section -->
    <div class="help-card">
        <h3>
            <i class="fas fa-info-circle"></i>
            Informações
        </h3>
        <div class="help-content">
            <p><strong>Quando usar esta funcionalidade:</strong></p>
            <ul>
                <li>Para registrar mensagens recebidas por outros canais (telefone, WhatsApp, etc.)</li>
                <li>Para criar registros de comunicação com clientes</li>
                <li>Para importar mensagens de outros sistemas</li>
                <li>Para testes do sistema de mensagens</li>
            </ul>
            
            <p><strong>Campos obrigatórios:</strong></p>
            <ul>
                <li>Nome do remetente</li>
                <li>Email do remetente</li>
                <li>Assunto da mensagem</li>
                <li>Conteúdo da mensagem</li>
            </ul>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function toggleReplyField() {
    const checkbox = document.querySelector('input[name="is_replied"]');
    const replySection = document.getElementById('replySection');
    
    if (checkbox.checked) {
        replySection.style.display = 'block';
        document.getElementById('admin_reply').focus();
    } else {
        replySection.style.display = 'none';
        document.getElementById('admin_reply').value = '';
    }
}

// Show reply section if it was checked before (on validation errors)
document.addEventListener('DOMContentLoaded', function() {
    const isRepliedCheckbox = document.querySelector('input[name="is_replied"]');
    if (isRepliedCheckbox && isRepliedCheckbox.checked) {
        toggleReplyField();
    }
});

// Auto-format phone number
document.getElementById('phone').addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    
    if (value.length >= 11) {
        value = value.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    } else if (value.length >= 7) {
        value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3');
    } else if (value.length >= 3) {
        value = value.replace(/(\d{2})(\d{0,5})/, '($1) $2');
    }
    
    e.target.value = value;
});

// Character counter for message
const messageTextarea = document.getElementById('message');
const messageGroup = messageTextarea.closest('.form-group');

// Create character counter
const counter = document.createElement('small');
counter.className = 'form-help character-counter';
counter.style.textAlign = 'right';
messageGroup.appendChild(counter);

function updateCounter() {
    const length = messageTextarea.value.length;
    counter.textContent = `${length} caracteres`;
    
    if (length > 1000) {
        counter.style.color = '#e53e3e';
    } else if (length > 800) {
        counter.style.color = '#d69e2e';
    } else {
        counter.style.color = '#718096';
    }
}

messageTextarea.addEventListener('input', updateCounter);
updateCounter(); // Initial count
</script>
@endsection

@section('styles')
<style>
.form-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 2rem;
    margin-bottom: 2rem;
}

.form-section {
    margin-bottom: 2rem;
    padding-bottom: 2rem;
    border-bottom: 1px solid #e2e8f0;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.section-title {
    margin-bottom: 1.5rem;
    color: #2d3748;
    font-size: 1.125rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1rem;
    margin-bottom: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-label {
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.form-label.required::after {
    content: ' *';
    color: #e53e3e;
}

.form-input,
.form-textarea {
    padding: 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
    transition: border-color 0.2s, box-shadow 0.2s;
}

.form-input:focus,
.form-textarea:focus {
    outline: none;
    border-color: #3182ce;
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.1);
}

.form-input.error,
.form-textarea.error {
    border-color: #e53e3e;
}

.form-textarea {
    resize: vertical;
    min-height: 120px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: 500;
    color: #4a5568;
    margin-bottom: 0.5rem;
}

.checkbox-label input[type="checkbox"] {
    margin-right: 0.5rem;
    transform: scale(1.2);
}

.form-help {
    color: #718096;
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.character-counter {
    display: block;
    margin-top: 0.5rem;
}

.error-message {
    color: #e53e3e;
    font-size: 0.75rem;
    margin-top: 0.25rem;
}

.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #e2e8f0;
}

.help-card {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1.5rem;
}

.help-card h3 {
    margin-bottom: 1rem;
    color: #2d3748;
    font-size: 1rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.help-content p {
    margin-bottom: 0.5rem;
    color: #4a5568;
    font-size: 0.875rem;
}

.help-content ul {
    margin: 0.5rem 0 1rem 1rem;
    color: #4a5568;
    font-size: 0.875rem;
}

.help-content li {
    margin-bottom: 0.25rem;
}

@media (max-width: 768px) {
    .form-row {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        flex-direction: column;
    }
}
</style>
@endsection