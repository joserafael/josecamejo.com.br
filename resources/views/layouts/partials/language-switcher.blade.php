@php
    use App\Helpers\LocalizationHelper;
    $languages = LocalizationHelper::getLanguageSwitcher();
    $currentLocale = LocalizationHelper::getCurrentLocale();
@endphp

<div class="language-switcher">
    <button class="language-toggle" type="button" id="languageDropdown" aria-expanded="false">
        <i class="fas fa-globe"></i>
        <span class="current-lang">
            @switch($currentLocale)
                @case('en')
                    EN
                    @break
                @case('es')
                    ES
                    @break
                @case('pt')
                    PT
                    @break
                @default
                    PT
            @endswitch
        </span>
        <i class="fas fa-chevron-down dropdown-arrow"></i>
    </button>
    <ul class="language-menu" id="languageMenu">
        @foreach($languages as $language)
            <li>
                <a class="language-item {{ $language['active'] ? 'active' : '' }}" 
                   href="{{ $language['url'] }}">
                    <span class="flag-icon">
                        @switch($language['code'])
                            @case('en')
                                🇺🇸
                                @break
                            @case('es')
                                🇪🇸
                                @break
                            @case('pt')
                                🇧🇷
                                @break
                        @endswitch
                    </span>
                    <span class="language-name">{{ $language['name'] }}</span>
                </a>
            </li>
        @endforeach
    </ul>
</div>

<style>
.language-switcher {
    position: relative;
    display: inline-block;
}

.language-toggle {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background-color: rgba(255, 255, 255, 0.9);
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 6px;
    color: #000;
    cursor: pointer;
    transition: all 0.3s ease;
    font-size: 14px;
    min-width: 80px;
}

.language-toggle:hover {
    background-color: rgba(255, 255, 255, 1);
    border-color: rgba(0, 0, 0, 0.3);
    transform: translateY(-1px);
    color: #000;
}

.language-toggle .dropdown-arrow {
    font-size: 12px;
    transition: transform 0.3s ease;
    margin-left: auto;
}

.language-toggle[aria-expanded="true"] .dropdown-arrow {
    transform: rotate(180deg);
}

.language-menu {
    position: absolute;
    top: 100%;
    right: 0;
    min-width: 140px;
    background-color: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(0, 0, 0, 0.2);
    border-radius: 8px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
    list-style: none;
    margin: 4px 0 0 0;
    padding: 4px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-10px);
    transition: all 0.3s ease;
    z-index: 1000;
}

.language-menu.show {
    opacity: 1;
    visibility: visible;
    transform: translateY(0);
}

.language-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    color: #000;
    text-decoration: none;
    border-radius: 4px;
    transition: all 0.3s ease;
    font-size: 14px;
}

.language-item:hover {
    background-color: rgba(255, 255, 255, 0.2);
    color: #000;
    text-decoration: none;
    transform: translateX(2px);
}

.language-item.active {
    background-color: rgba(74, 144, 226, 0.3);
    color: #4a90e2;
    font-weight: 600;
}

.language-item .flag-icon {
    font-size: 16px;
    width: 20px;
    text-align: center;
}

.language-item .language-name {
    flex: 1;
}

/* Mobile responsive */
@media (max-width: 768px) {
    .language-menu {
        right: -10px;
        min-width: 120px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const languageToggle = document.getElementById('languageDropdown');
    const languageMenu = document.getElementById('languageMenu');
    
    if (languageToggle && languageMenu) {
        languageToggle.addEventListener('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const isExpanded = languageToggle.getAttribute('aria-expanded') === 'true';
            
            if (isExpanded) {
                closeDropdown();
            } else {
                openDropdown();
            }
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function(e) {
            if (!languageToggle.contains(e.target) && !languageMenu.contains(e.target)) {
                closeDropdown();
            }
        });
        
        // Close dropdown on escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeDropdown();
            }
        });
        
        function openDropdown() {
            languageToggle.setAttribute('aria-expanded', 'true');
            languageMenu.classList.add('show');
        }
        
        function closeDropdown() {
            languageToggle.setAttribute('aria-expanded', 'false');
            languageMenu.classList.remove('show');
        }
    }
});
</script>