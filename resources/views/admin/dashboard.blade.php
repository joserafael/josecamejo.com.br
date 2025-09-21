@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">1,234</h3>
                <p class="stat-label">Usuários Totais</p>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    +12% este mês
                </span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">89</h3>
                <p class="stat-label">Posts Publicados</p>
                <span class="stat-change positive">
                    <i class="fas fa-arrow-up"></i>
                    +5% este mês
                </span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-folder-open"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">23</h3>
                <p class="stat-label">Projetos</p>
                <span class="stat-change neutral">
                    <i class="fas fa-minus"></i>
                    Sem alteração
                </span>
            </div>
        </div>

        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-content">
                <h3 class="stat-number">156</h3>
                <p class="stat-label">Mensagens</p>
                <span class="stat-change negative">
                    <i class="fas fa-arrow-down"></i>
                    -3% este mês
                </span>
            </div>
        </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="dashboard-grid">
        <!-- Chart Section -->
        <div class="dashboard-card chart-card">
            <div class="card-header">
                <h3>Visitantes do Site</h3>
                <div class="card-actions">
                    <select class="period-select">
                        <option value="7">Últimos 7 dias</option>
                        <option value="30" selected>Últimos 30 dias</option>
                        <option value="90">Últimos 90 dias</option>
                    </select>
                </div>
            </div>
            <div class="card-content">
                <div class="chart-placeholder">
                    <i class="fas fa-chart-line"></i>
                    <p>Gráfico de visitantes seria exibido aqui</p>
                    <small>Integre com Google Analytics ou similar</small>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="dashboard-card activity-card">
            <div class="card-header">
                <h3>Atividade Recente</h3>
                <a href="{{ route('admin.logs') }}" class="view-all-link">Ver todos</a>
            </div>
            <div class="card-content">
                <div class="activity-list">
                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="activity-content">
                            <p><strong>Novo usuário registrado</strong></p>
                            <span class="activity-time">2 minutos atrás</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="activity-content">
                            <p><strong>Post "Laravel Tips" publicado</strong></p>
                            <span class="activity-time">1 hora atrás</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div class="activity-content">
                            <p><strong>Nova mensagem de contato</strong></p>
                            <span class="activity-time">3 horas atrás</span>
                        </div>
                    </div>

                    <div class="activity-item">
                        <div class="activity-icon">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="activity-content">
                            <p><strong>Configurações atualizadas</strong></p>
                            <span class="activity-time">1 dia atrás</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions and System Info -->
    <div class="dashboard-grid">
        <!-- Quick Actions -->
        <div class="dashboard-card actions-card">
            <div class="card-header">
                <h3>Ações Rápidas</h3>
            </div>
            <div class="card-content">
                <div class="quick-actions">
                    <a href="{{ route('admin.posts.create') }}" class="quick-action">
                        <i class="fas fa-plus"></i>
                        <span>Novo Post</span>
                    </a>
                    <a href="{{ route('admin.projects.create') }}" class="quick-action">
                        <i class="fas fa-folder-plus"></i>
                        <span>Novo Projeto</span>
                    </a>
                    <a href="{{ route('admin.users.create') }}" class="quick-action">
                        <i class="fas fa-user-plus"></i>
                        <span>Novo Usuário</span>
                    </a>
                    <a href="{{ route('admin.backup') }}" class="quick-action">
                        <i class="fas fa-download"></i>
                        <span>Backup</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- System Info -->
        <div class="dashboard-card system-card">
            <div class="card-header">
                <h3>Informações do Sistema</h3>
            </div>
            <div class="card-content">
                <div class="system-info">
                    <div class="info-item">
                        <span class="info-label">Versão do Laravel:</span>
                        <span class="info-value">{{ app()->version() }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Versão do PHP:</span>
                        <span class="info-value">{{ phpversion() }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Ambiente:</span>
                        <span class="info-value">{{ app()->environment() }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Último Backup:</span>
                        <span class="info-value">Hoje às 03:00</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Dashboard Specific Styles */
.dashboard-container {
    max-width: 1400px;
    margin: 0 auto;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: var(--admin-surface);
    border-radius: 0.5rem;
    padding: 1.5rem;
    box-shadow: var(--admin-shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 3rem;
    height: 3rem;
    background: var(--admin-primary);
    color: white;
    border-radius: 0.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.stat-content {
    flex: 1;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin: 0 0 0.25rem 0;
    color: var(--admin-text);
}

.stat-label {
    margin: 0 0 0.5rem 0;
    color: var(--admin-text-muted);
    font-size: 0.875rem;
}

.stat-change {
    font-size: 0.75rem;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.stat-change.positive {
    color: var(--admin-success);
}

.stat-change.negative {
    color: var(--admin-danger);
}

.stat-change.neutral {
    color: var(--admin-text-muted);
}

.dashboard-grid {
    display: grid;
    grid-template-columns: 2fr 1fr;
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.dashboard-card {
    background: var(--admin-surface);
    border-radius: 0.5rem;
    box-shadow: var(--admin-shadow);
    overflow: hidden;
}

.card-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--admin-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.card-header h3 {
    margin: 0;
    font-size: 1.125rem;
    font-weight: 600;
}

.card-content {
    padding: 1.5rem;
}

.chart-placeholder {
    text-align: center;
    padding: 3rem 1rem;
    color: var(--admin-text-muted);
}

.chart-placeholder i {
    font-size: 3rem;
    margin-bottom: 1rem;
    display: block;
}

.activity-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.activity-icon {
    width: 2rem;
    height: 2rem;
    background: var(--admin-bg);
    color: var(--admin-primary);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.875rem;
    flex-shrink: 0;
}

.activity-content p {
    margin: 0 0 0.25rem 0;
    font-size: 0.875rem;
}

.activity-time {
    font-size: 0.75rem;
    color: var(--admin-text-muted);
}

.quick-actions {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 1rem;
}

.quick-action {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 1rem;
    background: var(--admin-bg);
    border-radius: 0.5rem;
    text-decoration: none;
    color: var(--admin-text);
    transition: all 0.2s ease;
}

.quick-action:hover {
    background: var(--admin-primary);
    color: white;
    transform: translateY(-2px);
}

.quick-action i {
    font-size: 1.5rem;
}

.system-info {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.5rem 0;
    border-bottom: 1px solid var(--admin-border);
}

.info-item:last-child {
    border-bottom: none;
}

.info-label {
    font-size: 0.875rem;
    color: var(--admin-text-muted);
}

.info-value {
    font-size: 0.875rem;
    font-weight: 500;
    color: var(--admin-text);
}

.period-select {
    padding: 0.25rem 0.5rem;
    border: 1px solid var(--admin-border);
    border-radius: 0.25rem;
    font-size: 0.875rem;
}

.view-all-link {
    color: var(--admin-primary);
    text-decoration: none;
    font-size: 0.875rem;
}

.view-all-link:hover {
    text-decoration: underline;
}

@media (max-width: 1024px) {
    .dashboard-grid {
        grid-template-columns: 1fr;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        grid-template-columns: 1fr;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Period selector functionality
    const periodSelect = document.querySelector('.period-select');
    if (periodSelect) {
        periodSelect.addEventListener('change', function() {
            // Here you would typically reload chart data
            console.log('Period changed to:', this.value);
        });
    }
});
</script>
@endpush
@endsection