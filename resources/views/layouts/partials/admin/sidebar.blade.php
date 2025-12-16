<aside class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-content">
        <!-- Main Navigation -->
        <nav class="sidebar-nav">
            <ul class="nav-list">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

               
               

                <!-- Blog Management -->
                <li class="nav-item">
                    <div class="nav-group">
                        <div class="nav-group-header">
                            <i class="fas fa-blog"></i>
                            <span>Blog</span>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.blog-posts.index') }}" class="nav-link {{ request()->routeIs('admin.blog-posts.*') ? 'active' : '' }}">
                        <i class="fas fa-newspaper"></i>
                        <span>Posts</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.blog-categories.index') }}" class="nav-link {{ request()->routeIs('admin.blog-categories.*') ? 'active' : '' }}">
                        <i class="fas fa-folder"></i>
                        <span>Categorias</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.blog-subcategories.index') }}" class="nav-link {{ request()->routeIs('admin.blog-subcategories.*') ? 'active' : '' }}">
                        <i class="fas fa-folder-open"></i>
                        <span>Subcategorias</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.blog-tags.index') }}" class="nav-link {{ request()->routeIs('admin.blog-tags.*') ? 'active' : '' }}">
                        <i class="fas fa-tags"></i>
                        <span>Tags</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.blog-images.index') }}" class="nav-link {{ request()->routeIs('admin.blog-images.*') ? 'active' : '' }}">
                        <i class="fas fa-images"></i>
                        <span>Imagens</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.blog-videos.index') }}" class="nav-link {{ request()->routeIs('admin.blog-videos.*') ? 'active' : '' }}">
                        <i class="fas fa-video"></i>
                        <span>Vídeos</span>
                    </a>
                </li>

                <!-- User Management -->
                <li class="nav-item">
                    <div class="nav-group">
                        <div class="nav-group-header">
                            <i class="fas fa-users"></i>
                            <span>Usuários</span>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.users.index') }}" class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                        <i class="fas fa-user"></i>
                        <span>Usuários</span>
                    </a>
                </li>

                <!-- Messages -->
                <li class="nav-item">
                    <a href="{{ route('admin.messages.index') }}" class="nav-link {{ request()->routeIs('admin.messages.*') ? 'active' : '' }}">
                        <i class="fas fa-envelope"></i>
                        <span>Mensagens</span>
                        @if(isset($unreadMessages) && $unreadMessages > 0)
                            <span class="nav-badge">{{ $unreadMessages }}</span>
                        @endif
                    </a>
                </li>

                <!-- Analytics -->
                <li class="nav-item">
                    <a href="{{ route('admin.analytics') }}" class="nav-link {{ request()->routeIs('admin.analytics') ? 'active' : '' }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>Analytics</span>
                    </a>
                </li>

                <!-- Settings -->
                <li class="nav-item">
                    <div class="nav-group">
                        <div class="nav-group-header">
                            <i class="fas fa-cog"></i>
                            <span>Sistema</span>
                        </div>
                    </div>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.settings.general') }}" class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}">
                        <i class="fas fa-sliders-h"></i>
                        <span>Configurações</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.backup') }}" class="nav-link {{ request()->routeIs('admin.backup*') ? 'active' : '' }}">
                        <i class="fas fa-download"></i>
                        <span>Backup</span>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="{{ route('admin.logs') }}" class="nav-link {{ request()->routeIs('admin.logs') ? 'active' : '' }}">
                        <i class="fas fa-list-alt"></i>
                        <span>Logs</span>
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Sidebar Footer -->
        <div class="sidebar-footer">
            <div class="sidebar-user-info">
                <img src="{{ asset('images/profile.jpg') }}" alt="Admin" class="sidebar-avatar">
                <div class="user-details">
                    <span class="user-name">{{ auth()->user()->name ?? 'Admin' }}</span>
                    <span class="user-role">Administrador</span>
                </div>
            </div>
        </div>
    </div>
</aside>

<!-- Sidebar Overlay for Mobile -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>