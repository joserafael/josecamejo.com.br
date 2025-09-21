<footer class="admin-footer">
    <div class="admin-footer-container ml-[280px]">
        <div class="footer-left">
            <p>&copy; {{ date('Y') }} José Rafael Camejo - Painel Administrativo</p>
        </div>
        
        <div class="footer-right">
            <div class="footer-links">
                <a href="{{ route('admin.help.index') }}" class="footer-link">
                    <i class="fas fa-question-circle"></i>
                    Ajuda
                </a>
                <a href="{{ route('admin.help.support') }}" class="footer-link">
                    <i class="fas fa-life-ring"></i>
                    Suporte
                </a>
                <a href="{{ route('home') }}" class="footer-link" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    Ver Site
                </a>
            </div>
            
            <div class="footer-version">
                <span class="version-info">v1.0.0</span>
            </div>
        </div>
    </div>
</footer>