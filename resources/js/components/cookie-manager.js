/**
 * Gerenciador de Cookies
 * Controla a exibição e aceitação do banner de cookies
 */

class CookieManager {
    constructor() {
        this.cookieName = 'cookie_consent';
        this.cookieExpireDays = 365;
        this.banner = null;
        this.acceptBtn = null;
        this.rejectBtn = null;
        
        this.init();
    }
    
    init() {
        // Aguarda o DOM estar carregado
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setup());
        } else {
            this.setup();
        }
    }
    
    setup() {
        this.banner = document.getElementById('cookie-banner');
        this.acceptBtn = document.getElementById('accept-cookies');
        this.rejectBtn = document.getElementById('reject-cookies');
        
        if (!this.banner) return;
        
        // Verifica se o usuário já fez uma escolha
        if (!this.hasConsent()) {
            this.showBanner();
        }
        
        // Adiciona event listeners
        if (this.acceptBtn) {
            this.acceptBtn.addEventListener('click', () => this.acceptCookies());
        }
        
        if (this.rejectBtn) {
            this.rejectBtn.addEventListener('click', () => this.rejectCookies());
        }
    }
    
    hasConsent() {
        return this.getCookie(this.cookieName) !== null;
    }
    
    showBanner() {
        if (this.banner) {
            this.banner.style.display = 'block';
            // Adiciona animação de entrada
            setTimeout(() => {
                this.banner.classList.add('show');
            }, 100);
        }
    }
    
    hideBanner() {
        if (this.banner) {
            this.banner.classList.add('hide');
            setTimeout(() => {
                this.banner.style.display = 'none';
                this.banner.classList.remove('show', 'hide');
            }, 300);
        }
    }
    
    acceptCookies() {
        this.setCookie(this.cookieName, 'accepted', this.cookieExpireDays);
        this.hideBanner();
        
        // Aqui você pode adicionar código para ativar cookies de analytics, etc.
        this.enableAnalytics();
        
        console.log('Cookies aceitos pelo usuário');
    }
    
    rejectCookies() {
        this.setCookie(this.cookieName, 'rejected', this.cookieExpireDays);
        this.hideBanner();
        
        // Remove cookies não essenciais
        this.disableAnalytics();
        
        console.log('Cookies rejeitados pelo usuário');
    }
    
    enableAnalytics() {
        // Aqui você pode adicionar código para Google Analytics, etc.
        // Exemplo:
        // if (typeof gtag !== 'undefined') {
        //     gtag('consent', 'update', {
        //         'analytics_storage': 'granted'
        //     });
        // }
    }
    
    disableAnalytics() {
        // Remove cookies de analytics se existirem
        this.deleteCookie('_ga');
        this.deleteCookie('_gid');
        this.deleteCookie('_gat');
        
        // Desabilita analytics
        // if (typeof gtag !== 'undefined') {
        //     gtag('consent', 'update', {
        //         'analytics_storage': 'denied'
        //     });
        // }
    }
    
    setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/;SameSite=Lax`;
    }
    
    getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }
    
    deleteCookie(name) {
        document.cookie = `${name}=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;`;
    }
    
    // Método público para verificar se cookies foram aceitos
    isAccepted() {
        return this.getCookie(this.cookieName) === 'accepted';
    }
    
    // Método público para resetar consentimento (útil para testes)
    resetConsent() {
        this.deleteCookie(this.cookieName);
        this.showBanner();
    }
}

// Inicializa o gerenciador de cookies
const cookieManager = new CookieManager();

// Exporta para uso global se necessário
window.CookieManager = cookieManager;

export default CookieManager;