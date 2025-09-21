@extends('layouts.admin')

@section('title', 'Mensagens - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1 class="page-title">
                <i class="fas fa-envelope"></i>
                Mensagens
            </h1>
            <p class="page-description">Gerencie todas as mensagens recebidas</p>
        </div>
        <div class="page-actions">
            <a href="{{ route('admin.messages.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i>
                Nova Mensagem
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="filters-section">
        <form method="GET" action="{{ route('admin.messages.index') }}">
            <div class="filters-grid">
                <div class="form-group">
                    <label for="search" class="form-label">Buscar:</label>
                    <input type="text" id="search" name="search" value="{{ request('search') }}" 
                           placeholder="Nome, email ou assunto..." class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="status" class="form-label">Status:</label>
                    <select id="status" name="status" class="form-select">
                        <option value="">Todos</option>
                        <option value="unread" {{ request('status') === 'unread' ? 'selected' : '' }}>
                            Não Lidas
                        </option>
                        <option value="read" {{ request('status') === 'read' ? 'selected' : '' }}>
                            Lidas
                        </option>
                        <option value="replied" {{ request('status') === 'replied' ? 'selected' : '' }}>
                            Respondidas
                        </option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>
                            Pendentes
                        </option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="date_from" class="form-label">De:</label>
                    <input type="date" id="date_from" name="date_from" 
                           value="{{ request('date_from') }}" 
                           class="form-input">
                </div>
                
                <div class="form-group">
                    <label for="date_to" class="form-label">Até:</label>
                    <input type="date" id="date_to" name="date_to" 
                           value="{{ request('date_to') }}" 
                           class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label">&nbsp;</label>
                    <div style="display: flex; gap: 0.5rem;">
                        <button type="submit" class="btn btn-secondary">
                            <i class="fas fa-search"></i>
                            Filtrar
                        </button>
                        <a href="{{ route('admin.messages.index') }}" class="btn btn-outline">
                            <i class="fas fa-times"></i>
                            Limpar
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Messages Table -->
    <div class="table-card">
        @if($messages->count() > 0)
            <div class="table-header">
                <div class="table-info">
                    <span class="results-count">
                        {{ $messages->total() }} mensagem(ns) encontrada(s)
                    </span>
                </div>
                <div class="table-actions">
                    <button type="button" class="btn btn-sm btn-info" onclick="markAllAsRead()">
                        <i class="fas fa-check-double"></i>
                        Marcar Todas como Lidas
                    </button>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Remetente</th>
                            <th>Assunto</th>
                            <th>Data</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($messages as $message)
                            <tr class="{{ !$message->is_read ? 'unread-row' : '' }}">
                                <td>
                                    <div class="status-badges">
                                        @if(!$message->is_read)
                                            <span class="badge badge-warning">
                                                <i class="fas fa-envelope"></i>
                                                Nova
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
                                </td>
                                <td>
                                    <div class="sender-info">
                                        <div class="sender-name">{{ $message->name }}</div>
                                        <div class="sender-email">{{ $message->email }}</div>
                                        @if($message->company)
                                            <div class="sender-company">{{ $message->company }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="message-subject">
                                        {{ Str::limit($message->subject, 50) }}
                                    </div>
                                    <div class="message-preview">
                                        {{ Str::limit($message->message, 100) }}
                                    </div>
                                </td>
                                <td>
                                    <div class="message-date">
                                        {{ $message->created_at->format('d/m/Y') }}
                                    </div>
                                    <div class="message-time">
                                        {{ $message->created_at->format('H:i') }}
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="{{ route('admin.messages.show', $message) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="Visualizar">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.messages.edit', $message) }}" 
                                           class="btn btn-sm btn-secondary" 
                                           title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete('{{ $message->id }}')" 
                                                title="Excluir">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        @if(!$message->is_read)
                                            <button type="button" 
                                                    class="btn btn-sm btn-info" 
                                                    onclick="markAsRead('{{ $message->id }}')" 
                                                    title="Marcar como lida">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $messages->appends(request()->query())->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 class="empty-title">Nenhuma mensagem encontrada</h3>
                <p class="empty-description">
                    @if(request()->hasAny(['search', 'status', 'date_from', 'date_to']))
                        Não foram encontradas mensagens com os filtros aplicados.
                        <a href="{{ route('admin.messages.index') }}">Limpar filtros</a>
                    @else
                        Ainda não há mensagens cadastradas no sistema.
                    @endif
                </p>
                <div class="empty-actions">
                    <a href="{{ route('admin.messages.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i>
                        Criar Primeira Mensagem
                    </a>
                </div>
            </div>
        @endif
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
            <form id="deleteForm" method="POST" style="display: inline;">
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
let deleteMessageId = null;

function confirmDelete(messageId) {
    deleteMessageId = messageId;
    document.getElementById('deleteForm').action = `/admin/messages/${messageId}`;
    document.getElementById('deleteModal').style.display = 'block';
}

function closeDeleteModal() {
    document.getElementById('deleteModal').style.display = 'none';
    deleteMessageId = null;
}

function markAsRead(messageId) {
    fetch(`/admin/messages/${messageId}/mark-as-read`, {
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

function markAllAsRead() {
    if (confirm('Marcar todas as mensagens como lidas?')) {
        fetch('/admin/messages/mark-all-as-read', {
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

.table-card {
    background: white;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.table-header {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #e2e8f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.results-count {
    color: #718096;
    font-size: 0.875rem;
}

.table-responsive {
    overflow-x: auto;
}

.data-table {
    width: 100%;
    border-collapse: collapse;
}

.data-table th {
    background: #f7fafc;
    padding: 1rem;
    text-align: left;
    font-weight: 600;
    color: #4a5568;
    border-bottom: 1px solid #e2e8f0;
}

.data-table td {
    padding: 1rem;
    border-bottom: 1px solid #e2e8f0;
    vertical-align: top;
}

.unread-row {
    background: #f0fff4;
}

.status-badges {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.badge {
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    font-size: 0.75rem;
    font-weight: 500;
    white-space: nowrap;
}

.badge-warning {
    background: #fef5e7;
    color: #d69e2e;
}

.badge-success {
    background: #f0fff4;
    color: #38a169;
}

.badge-info {
    background: #ebf8ff;
    color: #3182ce;
}

.sender-info {
    display: flex;
    flex-direction: column;
    gap: 0.25rem;
}

.sender-name {
    font-weight: 600;
    color: #2d3748;
}

.sender-email {
    color: #718096;
    font-size: 0.875rem;
}

.sender-company {
    color: #4a5568;
    font-size: 0.75rem;
    font-style: italic;
}

.message-subject {
    font-weight: 500;
    color: #2d3748;
    margin-bottom: 0.25rem;
}

.message-preview {
    color: #718096;
    font-size: 0.875rem;
    line-height: 1.4;
}

.message-date {
    font-weight: 500;
    color: #2d3748;
}

.message-time {
    color: #718096;
    font-size: 0.875rem;
}

.action-buttons {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
}

.btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

.btn-primary {
    background: #3182ce;
    color: white;
}

.btn-primary:hover {
    background: #2c5aa0;
}

.btn-secondary {
    background: #718096;
    color: white;
}

.btn-secondary:hover {
    background: #4a5568;
}

.btn-danger {
    background: #e53e3e;
    color: white;
}

.btn-danger:hover {
    background: #c53030;
}

.btn-info {
    background: #3182ce;
    color: white;
}

.btn-info:hover {
    background: #2c5aa0;
}

.btn-outline {
    background: transparent;
    color: #4a5568;
    border: 1px solid #d1d5db;
}

.btn-outline:hover {
    background: #f7fafc;
}

.pagination-wrapper {
    padding: 1rem 1.5rem;
    border-top: 1px solid #e2e8f0;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    font-size: 4rem;
    color: #cbd5e0;
    margin-bottom: 1rem;
}

.empty-title {
    font-size: 1.5rem;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.empty-description {
    color: #718096;
    margin-bottom: 2rem;
}

.empty-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
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

@media (max-width: 768px) {
    .filter-row {
        grid-template-columns: 1fr;
    }
    
    .filter-group.filter-search {
        grid-column: span 1;
    }
    
    .filter-actions {
        flex-direction: column;
        align-items: stretch;
    }
    
    .filter-actions-left,
    .filter-actions-right {
        justify-content: center;
    }
    
    .table-header {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch;
    }
    
    .action-buttons {
        justify-content: center;
    }
}
</style>
@endsection