@extends('layouts.admin')

@section('title', 'Dashboard - Admin')

@section('content')
<div class="dashboard-container">
    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card users-card">
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
            <div class="stat-decoration">
                <i class="fas fa-users"></i>
            </div>
        </div>

        <div class="stat-card posts-card">
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
            <div class="stat-decoration">
                <i class="fas fa-file-alt"></i>
            </div>
        </div>

        <div class="stat-card projects-card">
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
            <div class="stat-decoration">
                <i class="fas fa-folder-open"></i>
            </div>
        </div>

        <div class="stat-card messages-card">
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
            <div class="stat-decoration">
                <i class="fas fa-envelope"></i>
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
                    <a href="{{ route('admin.blog-comments.index') }}" class="quick-action">
                        <i class="fas fa-comments"></i>
                        <span>Comentários</span>
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