@extends('layouts.admin')

@section('title', 'Visualizar Mensagem - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-envelope-open"></i>
                Visualizar Mensagem
            </h1>
            <p class="page-description">Detalhes da mensagem de {{ $message->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.messages.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            <a href="{{ route('admin.messages.edit', $message) }}" class="btn btn-secondary">
                <i class="fas fa-edit"></i>
                Editar
            </a>
            @if(!$message->is_replied)
                <button type="button" class="btn btn-primary" onclick="showReplyForm()">
                    <i class="fas fa-reply"></i>
                    Responder
                </button>
            @endif
        </div>
    </div>

    <!-- Message Details -->
    <div class="message-details-card">
        <div class="message-header">
            <div class="message-status">
                @if(!$message->is_read)
                    <span class="badge badge-warning">
                        <i class="fas fa-envelope"></i>
                        Não Lida
                    </span>
                @else
                    <span class="badge badge-success">
                        <i class="fas fa-envelope-open"></i>
                        Lida
                    </span>
                @endif
                
                @if($message->is_replied)
                    <span class="badge badge-success">
                        <i class="fas fa-reply"></i>
                        Respondida
                    </span>
                @else
                    <span class="badge badge-info">
                        <i class="fas fa-clock"></i>
                        Pendente
                    </span>
                @endif
            </div>
            <div class="message-date">
                <i class="fas fa-calendar"></i>
                {{ $message->created_at->format('d/m/Y H:i') }}
            </div>
        </div>

        <div class="message-content">
            <div class="sender-section">
                <h3>Informações do Remetente</h3>
                <div class="sender-grid">
                    <div class="sender-field">
                        <label>Nome:</label>
                        <span>{{ $message->name }}</span>
                    </div>
                    <div class="sender-field">
                        <label>Email:</label>
                        <span>{{ $message->email }}</span>
                    </div>
                    @if($message->phone)
                        <div class="sender-field">
                            <label>Telefone:</label>
                            <span>{{ $message->phone }}</span>
                        </div>
                    @endif
                    @if($message->company)
                        <div class="sender-field">
                            <label>Empresa:</label>
                            <span>{{ $message->company }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <div class="message-section">
                <h3>Mensagem</h3>
                <div class="message-subject">
                    <label>Assunto:</label>
                    <span>{{ $message->subject }}</span>
                </div>
                <div class="message-body">
                    <label>Conteúdo:</label>
                    <div class="message-text">
                        {!! nl2br(e($message->message)) !!}
                    </div>
                </div>
            </div>

            @if($message->is_replied && $message->admin_reply)
                <div class="reply-section">
                    <h3>Resposta Enviada</h3>
                    <div class="reply-info">
                        <div class="reply-date">
                            <i class="fas fa-clock"></i>
                            Respondida em: {{ $message->replied_at->format('d/m/Y H:i') }}
                        </div>
                    </div>
                    <div class="reply-content">
                        {!! nl2br(e($message->admin_reply)) !!}
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Reply Form (Hidden by default) -->
    @if(!$message->is_replied)
        <div id="replyForm" class="reply-form-card" style="display: none;">
            <h3>Responder Mensagem</h3>
            <form method="POST" action="{{ route('admin.messages.reply', $message) }}">
                @csrf
                <div class="form-group">
                    <label for="reply" class="form-label required">Sua Resposta:</label>
                    <textarea id="reply" name="reply" rows="6" 
                              class="form-textarea @error('reply') error @enderror" 
                              placeholder="Digite sua resposta..." required></textarea>
                    @error('reply')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="hideReplyForm()">
                        Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i>
                        Enviar Resposta
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Actions -->
    <div class="message-actions">
        <div class="action-group">
            <h4>Ações Rápidas</h4>
            <div class="quick-actions">
                @if(!$message->is_read)
                    <button type="button" class="btn btn-info" onclick="markAsRead()">
                        <i class="fas fa-check"></i>
                        Marcar como Lida
                    </button>
                @endif
                
                <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" 
                   class="btn btn-secondary">
                    <i class="fas fa-envelope"></i>
                    Responder por Email
                </a>
                
                <button type="button" class="btn btn-danger" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i>
                    Excluir Mensagem
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirmar Exclusão</h3>
            <button type="button" class="modal-close" onclick="closeDeleteModal()">&times;</button>
        </div>
        <div class="modal-body">
            <p>Tem certeza que deseja excluir esta mensagem? Esta ação não pode ser desfeita.</p>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-outline" onclick="closeDeleteModal()">Cancelar</button>
            <form method="POST" action="{{ route('admin.messages.destroy', $message) }}" style="display: inline;">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Excluir</button>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
function showReplyForm() {
    document.getElementById('replyForm').style.display = 'block';
    document.getElementById('reply').focus();
}

function hideReplyForm() {
    document.getElementById('replyForm').style.display = 'none';
}

function confirmDelete() {
    document.getElementById('deleteModal').style.display = 'block';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
}

function markAsRead() {
    fetch(`/admin/messages/{{ $message->id }}/mark-as-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/json',
        },
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('deleteModal');
    if (event.target === modal) {
        closeDeleteModal();
    }
}
</script>
@endsection

@section('styles')
<style>
.message-details-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    margin-bottom: 2rem;
}

.message-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.message-status {
    display: flex;
    gap: 0.5rem;
}

.message-date {
    color: #718096;
    font-size: 0.875rem;
}

.message-content {
    padding: 1.5rem;
}

.sender-section,
.message-section,
.reply-section {
    margin-bottom: 2rem;
}

.sender-section h3,
.message-section h3,
.reply-section h3 {
    margin-bottom: 1rem;
    color: #2d3748;
    font-size: 1.125rem;
    font-weight: 600;
}

.sender-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}

.sender-field {
    display: flex;
    flex-direction: column;
}

.sender-field label {
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 0.25rem;
    font-size: 0.875rem;
}

.sender-field span {
    color: #2d3748;
}

.message-subject {
    margin-bottom: 1rem;
}

.message-subject label,
.message-body label {
    display: block;
    font-weight: 600;
    color: #4a5568;
    margin-bottom: 0.5rem;
    font-size: 0.875rem;
}

.message-subject span {
    color: #2d3748;
    font-size: 1.125rem;
    font-weight: 500;
}

.message-text {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 1rem;
    color: #2d3748;
    line-height: 1.6;
    white-space: pre-wrap;
}

.reply-section {
    background: #f0fff4;
    border: 1px solid #9ae6b4;
    border-radius: 6px;
    padding: 1rem;
}

.reply-info {
    margin-bottom: 1rem;
}

.reply-date {
    color: #38a169;
    font-size: 0.875rem;
    font-weight: 500;
}

.reply-content {
    background: white;
    border: 1px solid #c6f6d5;
    border-radius: 4px;
    padding: 1rem;
    color: #2d3748;
    line-height: 1.6;
    white-space: pre-wrap;
}

.reply-form-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.reply-form-card h3 {
    margin-bottom: 1rem;
    color: #2d3748;
}

.message-actions {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    padding: 1.5rem;
}

.action-group h4 {
    margin-bottom: 1rem;
    color: #2d3748;
    font-size: 1rem;
    font-weight: 600;
}

.quick-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.modal {
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 15% auto;
    padding: 0;
    border: none;
    border-radius: 8px;
    width: 90%;
    max-width: 500px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.modal-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h3 {
    margin: 0;
    color: #2d3748;
}

.modal-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    cursor: pointer;
    color: #718096;
}

.modal-body {
    padding: 1.5rem;
}

.modal-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
    display: flex;
    justify-content: flex-end;
    gap: 0.5rem;
}
</style>
@endsection