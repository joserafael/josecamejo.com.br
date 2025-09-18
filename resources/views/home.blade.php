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
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color: #333;
            background: linear-gradient(135deg, #000000 0%, #333333 100%);
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .header {
            padding: 20px 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            z-index: 1000;
            box-shadow: 0 2px 20px rgba(0, 0, 0, 0.1);
        }

        .nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 700;
            color: #000000;
        }

        .nav-links {
            display: flex;
            list-style: none;
            gap: 30px;
        }

        .nav-links a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .nav-links a:hover {
            color: #000000;
        }

        /* Hero Section */
        .hero {
            padding: 120px 0 80px;
            text-align: center;
            color: white;
        }

        .hero-content {
            display: flex;
            align-items: center;
            gap: 60px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .hero-text {
            flex: 1;
            text-align: left;
        }

        .hero-image {
            flex: 0 0 300px;
        }

        .profile-image {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            object-fit: cover;
            border: 8px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .profile-image:hover {
            transform: scale(1.05);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 20px;
            animation: fadeInUp 1s ease;
        }

        .hero .subtitle {
            font-size: 1.5rem;
            font-weight: 300;
            margin-bottom: 30px;
            animation: fadeInUp 1s ease 0.2s both;
        }

        .hero .description {
            font-size: 1.2rem;
            margin-bottom: 40px;
            opacity: 0.9;
            animation: fadeInUp 1s ease 0.4s both;
        }

        .cta-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
            flex-wrap: wrap;
            animation: fadeInUp 1s ease 0.6s both;
        }

        .btn {
            padding: 15px 30px;
            border: none;
            border-radius: 50px;
            font-size: 1.1rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s ease;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .btn-primary {
            background: white;
            color: #000000;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            background: #f0f0f0;
        }

        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }

        .btn-secondary:hover {
            background: white;
            color: #000000;
        }

        /* About Section */
        .about {
            padding: 80px 0;
            background: white;
        }

        .section-title {
            text-align: center;
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 60px;
            color: #333;
        }

        .about-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 60px;
            align-items: center;
        }

        .about-text {
            font-size: 1.1rem;
            line-height: 1.8;
        }

        .about-text h3 {
            font-size: 1.5rem;
            margin-bottom: 20px;
            color: #000000;
        }

        /* Skills Section */
        .skills {
            padding: 80px 0;
            background: #ffffff;
        }

        .skills-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .skill-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .skill-card:hover {
            transform: translateY(-5px);
        }

        .skill-card i {
            font-size: 3rem;
            color: #000000;
            margin-bottom: 20px;
        }

        .skill-card h4 {
            font-size: 1.2rem;
            margin-bottom: 10px;
            color: #333;
        }

        /* Contact Section */
        .contact {
            padding: 80px 0;
            background: #000000;
            color: white;
            text-align: center;
        }

        .social-links {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 40px;
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 60px;
            height: 60px;
            background: #ffffff;
            color: #000000;
            border-radius: 50%;
            text-decoration: none;
            font-size: 1.5rem;
            transition: all 0.3s ease;
        }

        .social-link:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(255, 255, 255, 0.4);
            background: #f0f0f0;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .hero-content {
                flex-direction: column-reverse;
                gap: 40px;
                text-align: center;
            }

            .hero-text {
                text-align: center;
            }

            .hero-image {
                flex: 0 0 200px;
            }

            .profile-image {
                width: 200px;
                height: 200px;
            }

            .hero h1 {
                font-size: 2.5rem;
            }

            .hero .subtitle {
                font-size: 1.2rem;
            }

            .about-content {
                grid-template-columns: 1fr;
                gap: 40px;
            }

            .nav-links {
                display: none;
            }

            .cta-buttons {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
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
    
    <script>
        // Smooth scrolling for navigation links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Header background on scroll
        window.addEventListener('scroll', function() {
            const header = document.querySelector('.header');
            if (window.scrollY > 100) {
                header.style.background = 'rgba(255, 255, 255, 0.98)';
            } else {
                header.style.background = 'rgba(255, 255, 255, 0.95)';
            }
        });
    </script>
</body>
</html>