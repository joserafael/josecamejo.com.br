@extends('layouts.admin')

@section('title', 'Editar Mensagem - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-edit"></i>
                Editar Mensagem
            </h1>
            <p class="page-description">Editar informações da mensagem de {{ $message->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
        </div>
    </div>

    <!-- Edit Form -->
    <div class="form-card">
        <form method="POST" action="{{ route('admin.messages.update', $message) }}">
            @csrf
            @method('PUT')
            
            <!-- Message Status -->
            <div class="form-section">
                <h3 class="section-title">Status da Mensagem</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_read" value="1" 
                                   {{ $message->is_read ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            Marcar como lida
                        </label>
                    </div>
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input type="checkbox" name="is_replied" value="1" 
                                   {{ $message->is_replied ? 'checked' : '' }}>
                            <span class="checkmark"></span>
                            Marcar como respondida
                        </label>
                    </div>
                </div>
            </div>

            <!-- Sender Information -->
            <div class="form-section">
                <h3 class="section-title">Informações do Remetente</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label for="name" class="form-label required">Nome:</label>
                        <input type="text" id="name" name="name" 
                               value="{{ old('name', $message->name) }}"
                               class="form-input @error('name') error @enderror" 
                               required>
                        @error('name')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label required">Email:</label>
                        <input type="email" id="email" name="email" 
                               value="{{ old('email', $message->email) }}"
                               class="form-input @error('email') error @enderror" 
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
                               value="{{ old('phone', $message->phone) }}"
                               class="form-input @error('phone') error @enderror">
                        @error('phone')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="company" class="form-label">Empresa:</label>
                        <input type="text" id="company" name="company" 
                               value="{{ old('company', $message->company) }}"
                               class="form-input @error('company') error @enderror">
                        @error('company')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Message Content -->
            <div class="form-section">
                <h3 class="section-title">Conteúdo da Mensagem</h3>
                <div class="form-group">
                    <label for="subject" class="form-label required">Assunto:</label>
                    <input type="text" id="subject" name="subject" 
                           value="{{ old('subject', $message->subject) }}"
                           class="form-input @error('subject') error @enderror" 
                           required>
                    @error('subject')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-group">
                    <label for="message" class="form-label required">Mensagem:</label>
                    <textarea id="message" name="message" rows="6" 
                              class="form-textarea @error('message') error @enderror" 
                              required>{{ old('message', $message->message) }}</textarea>
                    @error('message')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Admin Reply -->
            <div class="form-section">
                <h3 class="section-title">Resposta do Administrador</h3>
                <div class="form-group">
                    <label for="admin_reply" class="form-label">Resposta:</label>
                    <textarea id="admin_reply" name="admin_reply" rows="4" 
                              class="form-textarea @error('admin_reply') error @enderror"
                              placeholder="Digite a resposta do administrador...">{{ old('admin_reply', $message->admin_reply) }}</textarea>
                    @error('admin_reply')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                    <small class="form-help">
                        Se uma resposta for adicionada, a mensagem será automaticamente marcada como respondida.
                    </small>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <a href="{{ route('admin.messages.show', $message) }}" class="btn btn-outline">
                    Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i>
                    Salvar Alterações
                </button>
            </div>
        </form>
    </div>

    <!-- Message History -->
    <div class="history-card">
        <h3>Histórico da Mensagem</h3>
        <div class="history-timeline">
            <div class="timeline-item">
                <div class="timeline-marker">
                    <i class="fas fa-envelope"></i>
                </div>
                <div class="timeline-content">
                    <div class="timeline-title">Mensagem Recebida</div>
                    <div class="timeline-date">{{ $message->created_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
            
            @if($message->is_read)
                <div class="timeline-item">
                    <div class="timeline-marker success">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Mensagem Lida</div>
                        <div class="timeline-date">{{ $message->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            @endif
            
            @if($message->is_replied && $message->replied_at)
                <div class="timeline-item">
                    <div class="timeline-marker success">
                        <i class="fas fa-reply"></i>
                    </div>
                    <div class="timeline-content">
                        <div class="timeline-title">Mensagem Respondida</div>
                        <div class="timeline-date">{{ $message->replied_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
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
    min-height: 100px;
}

.checkbox-label {
    display: flex;
    align-items: center;
    cursor: pointer;
    font-weight: 500;
    color: #4a5568;
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

.history-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 2rem;
}

.history-card h3 {
    margin-bottom: 1.5rem;
    color: #2d3748;
    font-size: 1.125rem;
    font-weight: 600;
}

.history-timeline {
    position: relative;
    padding-left: 2rem;
}

.history-timeline::before {
    content: '';
    position: absolute;
    left: 1rem;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e2e8f0;
}

.timeline-item {
    position: relative;
    margin-bottom: 1.5rem;
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.timeline-marker {
    position: absolute;
    left: -2rem;
    width: 2rem;
    height: 2rem;
    background: #3182ce;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.75rem;
    z-index: 1;
}

.timeline-marker.success {
    background: #38a169;
}

.timeline-content {
    flex: 1;
}

.timeline-title {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.timeline-date {
    color: #718096;
    font-size: 0.875rem;
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