<header class="header">
    <nav class="nav container">
        <div class="logo">{{ isset($data['name']) ? explode(' ', $data['name'])[0] : 'José' }}</div>
        <ul class="nav-links">
            <li><a href="#home">Início</a></li>
            <li><a href="#about">Sobre</a></li>
            <li><a href="#skills">Habilidades</a></li>
            <li><a href="#contact">Contato</a></li>
        </ul>
    </nav>
</header>