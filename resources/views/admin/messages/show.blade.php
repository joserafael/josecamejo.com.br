@extends('layouts.admin')

@section('title', 'Visualizar Mensagem - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-envelope"></i>
                Visualizar Mensagem
            </h1>
            <p class="page-description">Detalhes completos da mensagem de {{ $message->name }}</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.messages.index') }}" class="btn btn-outline">
                <i class="fas fa-arrow-left"></i>
                Voltar
            </a>
            @if(!$message->is_read)
                <button type="button" class="btn btn-secondary" onclick="markAsRead()">
                    <i class="fas fa-check"></i>
                    Marcar como Lida
                </button>
            @endif
            <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" class="btn btn-primary">
                <i class="fas fa-reply"></i>
                Responder por Email
            </a>
        </div>
    </div>

    <!-- Message Status Card -->
    <div class="detail-card">
        <div class="detail-card-header">
            <div class="detail-card-title">
                <i class="fas fa-envelope-open"></i>
                <span>Status da Mensagem</span>
            </div>
            <div class="detail-card-status">
                <span class="status-badge {{ $message->is_read ? 'status-read' : 'status-unread' }}">
                    <i class="fas {{ $message->is_read ? 'fa-check-circle' : 'fa-circle' }}"></i>
                    {{ $message->is_read ? 'Lida' : 'Não Lida' }}
                </span>
            </div>
        </div>
        
        <div class="detail-card-body">
            <div class="message-overview">
                <div class="message-meta">
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>Recebida em {{ $message->created_at->format('d/m/Y \à\s H:i:s') }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-hashtag"></i>
                        <span>ID: {{ $message->id }}</span>
                    </div>
                    <div class="meta-item">
                        <i class="fas fa-tag"></i>
                        <span>{{ $message->subject }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Grid -->
    <div class="details-grid">
        <!-- Sender Information Card -->
        <div class="detail-card">
            <div class="detail-card-header">
                <div class="detail-card-title">
                    <i class="fas fa-user-circle"></i>
                    <span>Informações do Remetente</span>
                </div>
            </div>
            <div class="detail-card-body">
                <div class="sender-profile">
                    <div class="sender-avatar">
                        <div class="avatar-circle avatar-sender">
                            {{ strtoupper(substr($message->name, 0, 2)) }}
                        </div>
                    </div>
                    <div class="sender-details">
                        <h3 class="sender-name">{{ $message->name }}</h3>
                        <p class="sender-email">
                            <i class="fas fa-envelope"></i>
                            {{ $message->email }}
                        </p>
                    </div>
                </div>
                
                <div class="info-list">
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-user"></i>
                            Nome Completo
                        </div>
                        <div class="info-value">{{ $message->name }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-envelope"></i>
                            Endereço de Email
                        </div>
                        <div class="info-value">{{ $message->email }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-phone"></i>
                            Telefone
                        </div>
                        <div class="info-value">{{ $message->phone ?? 'Não informado' }}</div>
                    </div>
                    <div class="info-row">
                        <div class="info-label">
                            <i class="fas fa-building"></i>
                            Empresa
                        </div>
                        <div class="info-value">{{ $message->company ?? 'Não informado' }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Message Content Card -->
        <div class="detail-card">
            <div class="detail-card-header">
                <div class="detail-card-title">
                    <i class="fas fa-comment-alt"></i>
                    <span>Conteúdo da Mensagem</span>
                </div>
            </div>
            <div class="detail-card-body">
                <div class="message-subject-section">
                    <div class="subject-label">
                        <i class="fas fa-tag"></i>
                        Assunto
                    </div>
                    <div class="subject-content">{{ $message->subject }}</div>
                </div>
                
                <div class="message-content-section">
                    <div class="content-label">
                        <i class="fas fa-comment"></i>
                        Mensagem
                    </div>
                    <div class="content-text">
                        {!! nl2br(e($message->message)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Admin Reply Section (if exists) -->
    @if($message->is_replied && $message->admin_reply)
        <div class="detail-card">
            <div class="detail-card-header">
                <div class="detail-card-title">
                    <i class="fas fa-reply"></i>
                    <span>Resposta do Administrador</span>
                </div>
                <div class="detail-card-status">
                    <span class="status-badge status-replied">
                        <i class="fas fa-check-circle"></i>
                        Respondida
                    </span>
                </div>
            </div>
            <div class="detail-card-body">
                <div class="reply-meta">
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span>Respondida em {{ $message->replied_at ? $message->replied_at->format('d/m/Y H:i:s') : 'Data não disponível' }}</span>
                    </div>
                </div>
                <div class="reply-content">
                    {!! nl2br(e($message->admin_reply)) !!}
                </div>
            </div>
        </div>
    @endif

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
                @if(!$message->is_read)
                    <button type="button" class="action-button action-read" onclick="markAsRead('{{ $message->id }}')">
                        <div class="action-icon">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="action-content">
                            <div class="action-title">Marcar como Lida</div>
                            <div class="action-description">Marcar esta mensagem como lida</div>
                        </div>
                        <div class="action-arrow">
                            <i class="fas fa-chevron-right"></i>
                        </div>
                    </button>
                @endif

                <a href="mailto:{{ $message->email }}?subject=Re: {{ $message->subject }}" class="action-button action-reply">
                    <div class="action-icon">
                        <i class="fas fa-reply"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">Responder por Email</div>
                        <div class="action-description">Abrir cliente de email para resposta</div>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-external-link-alt"></i>
                    </div>
                </a>

                <button type="button" class="action-button action-delete" onclick="confirmDelete('{{ $message->id }}')">
                    <div class="action-icon">
                        <i class="fas fa-trash-alt"></i>
                    </div>
                    <div class="action-content">
                        <div class="action-title">Excluir Mensagem</div>
                        <div class="action-description">Remover permanentemente</div>
                    </div>
                    <div class="action-arrow">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Form (hidden) -->
<form id="deleteForm" action="{{ route('admin.messages.destroy', $message) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@endsection

@section('styles')
<link rel="stylesheet" href="{{ asset('css/admin-message-show.css') }}">
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

function markAsRead(messageId) {
    fetch(`/admin/messages/${messageId}/mark-read`, {
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
        alert('Erro ao marcar mensagem como lida');
    });
}

function confirmDelete(messageId) {
    if (confirm('Tem certeza que deseja excluir esta mensagem? Esta ação não pode ser desfeita.')) {
        document.getElementById('deleteForm').submit();
    }
}
</script>
@endsection