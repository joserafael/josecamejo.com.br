<footer class="footer">
    <div class="container">
        <p>&copy; {{ date('Y') }} {{ isset($data['name']) ? $data['name'] : 'José Rafael Camejo' }}. Todos os direitos reservados.</p>
        <p>
            <a href="{{ route('privacy.policy') }}" class="privacy-link">Política de Privacidade</a>
        </p>
    </div>
</footer>

<!-- Alerta de Cookies -->
<div id="cookie-banner" class="cookie-banner" style="display: none;">
    <div class="cookie-content">
        <div class="cookie-text">
            <i class="fas fa-cookie-bite"></i>
            <span>Este site usa cookies e outras tecnologias semelhantes para melhorar a sua experiência, analisar o tráfego do site e personalizar o conteúdo. Conheça a nossa <a href="{{ route('privacy.policy') }}" target="_blank">política de privacidade</a> e, ao continuar navegando, você concorda com estas condições.</span>
        </div>
        <div class="cookie-actions">
            <button id="accept-cookies" class="btn-accept">Aceitar</button>
            <button id="reject-cookies" class="btn-reject">Rejeitar</button>
        </div>
    </div>
</div>