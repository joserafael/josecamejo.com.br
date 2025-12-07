@extends('layouts.app')

@section('title', $data['name'] . ' - ' . $data['title'])
@section('description', $data['description'])

@push('styles')
    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@push('scripts')
    <!-- External JS -->
    <script src="{{ asset('js/home.js') }}"></script>
@endpush

@push('vue-components')
    <home-component></home-component>
@endpush

@section('content')

    <!-- Hero Section -->
    <section id="home" class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1>{{ $data['name'] }}</h1>
                    <p class="subtitle">{{ $data['title'] }}</p>
                    <p class="description">{{ $data['description'] }}</p>
                    <div class="cta-buttons">
                        <a href="#about" class="btn btn-primary">
                            <i class="fas fa-user"></i>
                            {{ __('messages.about_me') }}
                        </a>
                        <a href="#contact" class="btn btn-secondary">
                            <i class="fas fa-envelope"></i>
                            {{ __('messages.contact_me') }}
                        </a>
                    </div>
                </div>
                <div class="hero-image">
                    <img src="{{ $data['profile_image'] }}" alt="{{ $data['name'] }}" class="profile-image">
                </div>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="about">
        <div class="container">
            <h2 class="section-title">{{ __('messages.about_me') }}</h2>
            <div class="about-content">
                <div class="about-text">
                    <h3>{{ __('messages.welcome') }}! {{ __('messages.full_stack_developer') }}</h3>
                    <p>
                        {{ __('messages.about_description') }}
                    </p>
                </div>
                <div class="about-image">
                    <div style="width: 100%; height: 400px; background: linear-gradient(135deg, #000000 0%, #333333 100%); border-radius: 15px; display: flex; align-items: center; justify-content: center; color: white; font-size: 4rem;">
                        <i class="fas fa-code"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Skills Section -->
    <section id="skills" class="skills">
        <div class="container">
            <h2 class="section-title">{{ __('messages.skills') }}</h2>
            <div class="skills-grid">
                @foreach($data['skills'] as $skill)
                <div class="skill-card">
                    <i class="{{ $skill['icon'] }} skill-icon"></i>
                    <h4>{{ $skill['name'] }}</h4>
                    <p>{{ $skill['description'] }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Contact Section -->
    <section id="contact" class="contact">
        <div class="container">
            <h2 class="section-title">{{ __('messages.get_in_touch') }}</h2>
            <p class="contact-description">{{ __('messages.contact_description') }}</p>
            
            <!-- Contact Form -->
            <div class="contact-content">
                <div class="contact-form-container">
                    @if(session('success'))
                        <div class="alert alert-success">
                            <i class="fas fa-check-circle"></i>
                            {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('send.message') }}" method="POST" class="contact-form" id="contactForm">
                        @csrf
                        <div class="form-row">
                            <div class="form-group">
                                <label for="name">{{ __('messages.name') }} *</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="email">{{ __('messages.email') }} *</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required>
                                @error('email')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="phone">{{ __('messages.phone') }}</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone') }}">
                                @error('phone')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="company">{{ __('messages.company') }}</label>
                                <input type="text" id="company" name="company" value="{{ old('company') }}">
                                @error('company')
                                    <span class="error-message">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="subject">{{ __('messages.subject') }} *</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject') }}" required>
                            @error('subject')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="message">{{ __('messages.message') }} *</label>
                            <textarea id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                            @error('message')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Captcha -->
                        <div class="form-group captcha-group">
                            <label for="captcha_answer">{{ __('messages.solve_math') }} *</label>
                            <div class="captcha-container">
                                <span class="captcha-question" id="captchaQuestion">{{ __('messages.solve_math') }}...</span>
                                <input type="number" id="captcha_answer" name="captcha_answer" required placeholder="{{ __('messages.your_answer') }}">
                                <button type="button" class="captcha-refresh" id="refreshCaptcha" title="{{ __('messages.solve_math') }}">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </div>
                            @error('captcha_answer')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="fas fa-paper-plane"></i>
                            {{ __('messages.send_message') }}
                        </button>
                    </form>
                </div>

                <div class="contact-info">
                    <h3>{{ __('messages.other_contact_methods') }}</h3>
                    <div class="social-links">
                        <a href="{{ $data['social']['github'] }}" class="social-link" target="_blank">
                            <i class="fab fa-github"></i>
                            <span>GitHub</span>
                        </a>
                        <a href="{{ $data['social']['linkedin'] }}" class="social-link" target="_blank">
                            <i class="fab fa-linkedin"></i>
                            <span>LinkedIn</span>
                        </a>
                        <a href="{{ $data['social']['bluesky'] }}" class="social-link" target="_blank">
                            <i class="fa-brands fa-bluesky"></i>
                            <span>Bluesky</span>
                        </a>
                        <a href="{{ $data['social']['X'] }}" class="social-link" target="_blank">
                            <i class="fab fa-twitter"></i>
                            <span>X (Twitter)</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection