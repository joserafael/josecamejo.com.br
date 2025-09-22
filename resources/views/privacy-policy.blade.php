@extends('layouts.privacy')

@section('title', 'Política de Privacidade - José Rafael Camejo')
@section('description', 'Política de privacidade do site josecamejo.com.br - Como coletamos, usamos e protegemos seus dados pessoais')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/privacy-policy.css') }}">
@endpush

@section('content')
<div class="privacy-policy">
    <h1>Política de Privacidade</h1>
    
    <div class="last-updated">
        <p>Última atualização: {{ date('d/m/Y') }}</p>
    </div>
    
    <h2>1. Introdução</h2>
    <p>Esta Política de Privacidade descreve como o site <strong>josecamejo.com.br</strong> ("nós", "nosso" ou "site") coleta, usa e protege as informações que você fornece quando utiliza nosso site.</p>
    
    <p>Ao acessar e usar este site, você concorda com a coleta e uso de informações de acordo com esta política. Se você não concordar com qualquer parte desta política, recomendamos que não utilize nosso site.</p>
    
    <h2>2. Informações que Coletamos</h2>
    
    <h3>2.1 Informações Fornecidas Voluntariamente</h3>
    <p>Coletamos informações que você nos fornece diretamente quando:</p>
    <ul>
        <li>Preenche o formulário de contato</li>
        <li>Envia mensagens através do site</li>
        <li>Interage com nosso conteúdo</li>
    </ul>
    
    <p>Essas informações podem incluir:</p>
    <ul>
        <li>Nome completo</li>
        <li>Endereço de e-mail</li>
        <li>Número de telefone (opcional)</li>
        <li>Nome da empresa (opcional)</li>
        <li>Assunto e conteúdo da mensagem</li>
    </ul>
    
    <h3>2.2 Informações Coletadas Automaticamente</h3>
    <p>Quando você visita nosso site, coletamos automaticamente certas informações, incluindo:</p>
    <ul>
        <li>Endereço IP</li>
        <li>Tipo de navegador e versão</li>
        <li>Sistema operacional</li>
        <li>Páginas visitadas e tempo gasto no site</li>
        <li>Data e hora de acesso</li>
        <li>Site de referência</li>
    </ul>
    
    <h2>3. Como Usamos suas Informações</h2>
    <p>Utilizamos as informações coletadas para:</p>
    <ul>
        <li>Responder às suas mensagens e solicitações</li>
        <li>Melhorar nosso site e serviços</li>
        <li>Analisar o tráfego e uso do site</li>
        <li>Personalizar sua experiência de navegação</li>
        <li>Detectar e prevenir atividades fraudulentas</li>
        <li>Cumprir obrigações legais</li>
    </ul>
    
    <h2>4. Cookies e Tecnologias Similares</h2>
    <p>Nosso site utiliza cookies e outras tecnologias similares para:</p>
    <ul>
        <li>Melhorar a funcionalidade do site</li>
        <li>Analisar o tráfego e comportamento dos usuários</li>
        <li>Personalizar conteúdo e experiência</li>
        <li>Lembrar suas preferências</li>
    </ul>
    
    <p>Você pode controlar o uso de cookies através das configurações do seu navegador. No entanto, desabilitar cookies pode afetar a funcionalidade do site.</p>
    
    <h2>5. Compartilhamento de Informações</h2>
    <p>Não vendemos, alugamos ou compartilhamos suas informações pessoais com terceiros, exceto nas seguintes situações:</p>
    <ul>
        <li>Com seu consentimento explícito</li>
        <li>Para cumprir obrigações legais</li>
        <li>Para proteger nossos direitos e propriedade</li>
        <li>Em caso de fusão, aquisição ou venda de ativos</li>
    </ul>
    
    <h2>6. Segurança dos Dados</h2>
    <p>Implementamos medidas de segurança técnicas e organizacionais apropriadas para proteger suas informações pessoais contra acesso não autorizado, alteração, divulgação ou destruição.</p>
    
    <p>No entanto, nenhum método de transmissão pela internet ou armazenamento eletrônico é 100% seguro, e não podemos garantir segurança absoluta.</p>
    
    <h2>7. Retenção de Dados</h2>
    <p>Mantemos suas informações pessoais apenas pelo tempo necessário para cumprir os propósitos descritos nesta política, a menos que um período de retenção mais longo seja exigido ou permitido por lei.</p>
    
    <h2>8. Seus Direitos</h2>
    <p>Você tem o direito de:</p>
    <ul>
        <li>Acessar suas informações pessoais</li>
        <li>Corrigir informações incorretas ou incompletas</li>
        <li>Solicitar a exclusão de suas informações</li>
        <li>Retirar seu consentimento a qualquer momento</li>
        <li>Solicitar a portabilidade de seus dados</li>
    </ul>
    
    <h2>9. Links para Sites de Terceiros</h2>
    <p>Nosso site pode conter links para sites de terceiros. Esta política de privacidade não se aplica a esses sites. Recomendamos que você leia as políticas de privacidade de qualquer site de terceiro que visitar.</p>
    
    <h2>10. Alterações nesta Política</h2>
    <p>Podemos atualizar esta Política de Privacidade periodicamente. Notificaremos sobre mudanças significativas publicando a nova política em nosso site com uma nova data de "última atualização".</p>
    
    <h2>11. Contato</h2>
    <div class="contact-info">
        <p><strong>Para questões sobre esta Política de Privacidade, entre em contato:</strong></p>
        <p>
            <strong>E-mail:</strong> contato@josecamejo.com.br<br>
            <strong>Site:</strong> <a href="{{ route('home') }}">josecamejo.com.br</a>
        </p>
    </div>
    
    <h2>12. Lei Aplicável</h2>
    <p>Esta Política de Privacidade é regida pelas leis brasileiras, incluindo a Lei Geral de Proteção de Dados (LGPD - Lei nº 13.709/2018).</p>
</div>
@endsection