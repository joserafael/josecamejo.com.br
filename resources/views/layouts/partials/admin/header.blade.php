<header class="admin-header">
    <div class="admin-header-container ml-[280px]">
        <!-- Logo/Brand -->
        <div class="admin-brand">
            <a href="{{ route('admin.dashboard') }}" class="admin-logo">
                <i class="fas fa-cog"></i>
                <span>Admin Panel</span>
            </a>
        </div>

        <!-- Mobile Menu Toggle -->
        <button class="admin-mobile-toggle" id="adminMobileToggle" aria-label="Toggle admin menu">
            <i class="fas fa-bars"></i>
        </button>

        <!-- Header Actions -->
        <div class="admin-header-actions">
            <!-- Notifications -->
            <div class="admin-notification-dropdown">
                <button class="admin-notification-btn" aria-label="Notifications">
                    <i class="fas fa-bell"></i>
                    <span class="notification-badge">3</span>
                </button>
                <div class="notification-dropdown-content">
                    <div class="notification-header">
                        <h4>Notificações</h4>
                        <a href="#" class="mark-all-read">Marcar todas como lidas</a>
                    </div>
                    <div class="notification-list">
                        <div class="notification-item unread">
                            <i class="fas fa-user-plus"></i>
                            <div class="notification-content">
                                <p>Novo usuário registrado</p>
                                <span class="notification-time">2 min atrás</span>
                            </div>
                        </div>
                        <div class="notification-item">
                            <i class="fas fa-envelope"></i>
                            <div class="notification-content">
                                <p>Nova mensagem de contato</p>
                                <span class="notification-time">1 hora atrás</span>
                            </div>
                        </div>
                    </div>
                    <div class="notification-footer">
                        <a href="{{ route('admin.notifications.index') }}">Ver todas</a>
                    </div>
                </div>
            </div>

            <!-- User Profile -->
            <div class="admin-user-dropdown">
                <button class="admin-user-btn" aria-label="User menu">
                    <img src="{{ asset('images/profile.jpg') }}" alt="Admin" class="admin-avatar">
                    <span class="admin-username">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="user-dropdown-content">
                    <a href="{{ route('admin.settings.profile') }}" class="dropdown-item">
                        <i class="fas fa-user"></i>
                        Meu Perfil
                    </a>
                    <a href="{{ route('admin.settings.general') }}" class="dropdown-item">
                        <i class="fas fa-cog"></i>
                        Configurações
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="{{ route('home') }}" class="dropdown-item" target="_blank">
                        <i class="fas fa-external-link-alt"></i>
                        Ver Site
                    </a>
                    <div class="dropdown-divider"></div>
                    <form method="POST" action="{{ route('logout') }}" class="dropdown-form">
                        @csrf
                        <button type="submit" class="dropdown-item logout-btn">
                            <i class="fas fa-sign-out-alt"></i>
                            Sair
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>