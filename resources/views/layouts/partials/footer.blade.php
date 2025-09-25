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
            <span>{{ __('messages.cookie_notice') }} <a href="{{ route('privacy.policy') }}" target="_blank">{{ __('messages.privacy_policy') }}</a> {{ __('messages.cookie_agreement') }}</span>
        </div>
        <div class="cookie-actions">
            <button id="accept-cookies" class="btn-accept">{{ __('messages.accept_cookies') }}</button>
            <button id="reject-cookies" class="btn-reject">{{ __('messages.reject_cookies') }}</button>
        </div>
    </div>
</div>