<footer class="footer">
    <div class="container">
        <p>&copy; {{ date('Y') }} {{ isset($data['name']) ? $data['name'] : 'José Rafael Camejo' }}. Todos os direitos reservados.</p>
        <div class="social-links">
            @if(isset($data['social']))
                <a href="{{ $data['social']['github'] }}" class="social-link" target="_blank">
                    <i class="fab fa-github"></i>
                </a>
                <a href="{{ $data['social']['linkedin'] }}" class="social-link" target="_blank">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="mailto:{{ $data['social']['email'] }}" class="social-link">
                    <i class="fas fa-envelope"></i>
                </a>
            @endif
        </div>
    </div>
</footer>