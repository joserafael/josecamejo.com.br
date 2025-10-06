<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id={{ env('GOOGLE_ANALYTICS_ID') }}"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', "{{ env('GOOGLE_ANALYTICS_ID') }}");
</script>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Blog - José Rafael Camejo')</title>
    <meta name="description" content="@yield('description', 'Blog sobre desenvolvimento web, Laravel, Vue.js e tecnologias modernas')">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    <link rel="stylesheet" href="{{ asset('css/cookie-banner.css') }}">
    <link rel="stylesheet" href="{{ asset('css/comments.css') }}">
    <link rel="stylesheet" href="{{ asset('css/blog.css') }}">
    
    <!-- Page Specific CSS -->
    @stack('styles')
</head>
<body>
    <div id="app">
        <!-- Vue Components -->
        @stack('vue-components')
        
        <!-- Blog Header (Simple Navigation) -->
        <header class="blog-header">
            <div class="container">
                <nav class="blog-nav">
                    <a href="{{ route('blog.index') }}" class="blog-logo">
                        <i class="fas fa-blog"></i>
                        Blog
                    </a>
                    <div class="blog-nav-links">
                        <a href="{{ route('blog.index') }}">Todos os Posts</a>
                        <a href="{{ route('home') }}">Voltar ao Site</a>
                    </div>
                </nav>
            </div>
        </header>

        <!-- Main Content -->
        <main class="blog-main">
            @yield('content')
        </main>

        <!-- Footer -->
        @include('layouts.partials.footer')
    </div>
    
    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    <script src="{{ asset('js/blog.js') }}"></script>
    
    <!-- Page Specific Scripts -->
    @stack('scripts')
</body>
</html>