<header class="header">
    <nav class="nav container">
        <div class="logo"></div>
        
        <!-- Mobile Menu Toggle -->
        <button class="mobile-menu-toggle" aria-label="Toggle mobile menu">
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
            <span class="hamburger-line"></span>
        </button>
        
        <ul class="nav-links">
            <li><a href="#home">{{ __('messages.home') }}</a></li>
            <li><a href="#about">{{ __('messages.about') }}</a></li>
            <li><a href="#skills">{{ __('messages.skills') }}</a></li>
            <li><a href="#contact">{{ __('messages.contact') }}</a></li>
            <li><a href="/blog">{{ __('messages.blog') }}</a></li>
        </ul>
        
        <!-- Language Switcher -->
        <div class="nav-language">
            @include('layouts.partials.language-switcher')
        </div>
    </nav>
</header>