<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'José Rafael Camejo')</title>
    <meta name="description" content="@yield('description', 'Desenvolvedor Full Stack especializado em Laravel, Vue.js e tecnologias modernas')">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    
    <!-- Styles -->
    @vite(['resources/css/app.css'])
    
    <!-- Page Specific CSS -->
    @stack('styles')
</head>
<body>
    <div id="app">
        <!-- Vue Components -->
        @stack('vue-components')
        
        <!-- Header -->
        @include('layouts.partials.header')

        <!-- Main Content -->
        <main>
            @yield('content')
        </main>

        <!-- Footer -->
        @include('layouts.partials.footer')
    </div>
    
    <!-- Scripts -->
    @vite(['resources/js/app.js'])
    
    <!-- Page Specific Scripts -->
    @stack('scripts')
</body>
</html>