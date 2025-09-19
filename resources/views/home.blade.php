<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $data['name'] }} - {{ $data['title'] }}</title>
    <meta name="description" content="{{ $data['description'] }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    
    <!-- External CSS -->
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <nav class="nav container">
            <div class="logo">{{ explode(' ', $data['name'])[0] }}</div>
            <ul class="nav-links">
                <li><a href="#home">Início</a></li>
                <li><a href="#about">Sobre</a></li>
                <li><a href="#skills">Habilidades</a></li>
                <li><a href="#contact">Contato</a></li>
            </ul>
        </nav>
    </header>

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
                            Conheça mais
                        </a>
                        <a href="#contact" class="btn btn-secondary">
                            <i class="fas fa-envelope"></i>
                            Entre em contato
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
            <h2 class="section-title">Sobre Mim</h2>
            <div class="about-content">
                <div class="about-text">
                    <h3>Olá! Eu sou {{ $data['name'] }}</h3>
                    <p>
                        Desenvolvedor de Software Sênior com mais de 25 anos de experiência em projetar, desenvolver e manter sistemas web robustos e escaláveis. Sólida expertise em tecnologias back-end, incluindo Ruby on Rails, PHP (com frameworks como Laravel e CakePHP) e Python.
                    </p>
                    <p>
                        Capacidade comprovada de modernizar sistemas legados, migrar tecnologias e otimizar o desempenho de bancos de dados. Proativo, com excelentes habilidades para resolver problemas e acostumado a trabalhar em times ágeis (Scrum/Kanban) para entregar soluções de alta qualidade.
                    </p>
                    <p>
                        Original da Venezuela e morando em São Paulo há 7 anos, tenho uma perspectiva multicultural e ótima adaptabilidade.
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
            <h2 class="section-title">Minhas Habilidades</h2>
            <div class="skills-grid">
                @foreach($data['skills'] as $skill)
                <div class="skill-card">
                    <i class="fas fa-code"></i>
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
            <h2 class="section-title">Vamos Conversar?</h2>
            <p>Estou sempre aberto a novos projetos e oportunidades. Entre em contato!</p>
            <div class="social-links">
                <a href="{{ $data['social']['github'] }}" class="social-link" target="_blank">
                    <i class="fab fa-github"></i>
                </a>
                <a href="{{ $data['social']['linkedin'] }}" class="social-link" target="_blank">
                    <i class="fab fa-linkedin"></i>
                </a>
                <a href="mailto:{{ $data['social']['email'] }}" class="social-link">
                    <i class="fas fa-envelope"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    
    <!-- External JavaScript -->
    <script src="{{ asset('js/home.js') }}"></script>
</body>
</html>