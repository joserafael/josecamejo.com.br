#!/bin/bash

# Script para preparar deploy Laravel para cPanel (FTP)
# Autor: José Camejo
# Data: $(date +%Y-%m-%d)

echo "🚀 Iniciando build para deploy cPanel..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para log colorido
log() {
    echo -e "${GREEN}[$(date +'%H:%M:%S')]${NC} $1"
}

error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

# Verificar se estamos no diretório correto
if [ ! -f "artisan" ]; then
    error "Este script deve ser executado na raiz do projeto Laravel!"
    exit 1
fi

# Criar pasta de deploy
DEPLOY_DIR="deploy-cpanel"
log "Criando pasta de deploy: $DEPLOY_DIR"

if [ -d "$DEPLOY_DIR" ]; then
    warning "Pasta $DEPLOY_DIR já existe. Removendo..."
    rm -rf "$DEPLOY_DIR"
fi

mkdir -p "$DEPLOY_DIR"
mkdir -p "$DEPLOY_DIR/public_html"
mkdir -p "$DEPLOY_DIR/laravel_app"

# Instalar dependências de produção
log "Instalando dependências de produção..."
composer install --no-dev --optimize-autoloader --no-interaction

# Compilar assets do Vite
log "Compilando assets do Vite para produção..."
if [ -f "package.json" ]; then
    if command -v npm &> /dev/null; then
        npm install
        npm run build
        log "✅ Assets do Vite compilados com sucesso!"
    else
        warning "NPM não encontrado. Pulando compilação do Vite."
        warning "Certifique-se de compilar os assets manualmente: npm run build"
    fi
else
    warning "package.json não encontrado. Pulando compilação do Vite."
fi

# Otimizar aplicação
log "Otimizando aplicação para produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Copiar arquivos da aplicação (exceto public)
log "Copiando arquivos da aplicação..."
rsync -av --exclude='public' \
          --exclude='node_modules' \
          --exclude='.git' \
          --exclude='tests' \
          --exclude='storage/logs/*' \
          --exclude='storage/framework/cache/*' \
          --exclude='storage/framework/sessions/*' \
          --exclude='storage/framework/views/*' \
          --exclude='deploy-cpanel' \
          --exclude='.env' \
          . "$DEPLOY_DIR/laravel_app/"

# Copiar pasta public para public_html
log "Copiando pasta public para public_html..."
cp -r public/* "$DEPLOY_DIR/public_html/"

# Criar index.php modificado para cPanel
log "Criando index.php para cPanel..."
cat > "$DEPLOY_DIR/public_html/index.php" << 'EOF'
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Check If The Application Is Under Maintenance
|--------------------------------------------------------------------------
|
| If the application is in maintenance / demo mode via the "down" command
| we will load this file so that any pre-rendered content can be shown
| instead of starting the framework, which could cause an exception.
|
*/

if (file_exists($maintenance = __DIR__.'/../laravel_app/storage/framework/maintenance.php')) {
    require $maintenance;
}

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| this application. We just need to utilize it! We'll simply require it
| into the script here so we don't need to manually load our classes.
|
*/

require __DIR__.'/../laravel_app/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request using
| the application's HTTP kernel. Then, we will send the response back
| to this client's browser, allowing them to enjoy our application.
|
*/

$app = require_once __DIR__.'/../laravel_app/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
EOF

# Copiar arquivo .env.production como .env
log "Copiando configuração de produção..."
cp .env.production "$DEPLOY_DIR/laravel_app/.env"

# Criar pastas de storage necessárias
log "Criando estrutura completa de storage..."
mkdir -p "$DEPLOY_DIR/laravel_app/storage/app/public"
mkdir -p "$DEPLOY_DIR/laravel_app/storage/framework/cache/data"
mkdir -p "$DEPLOY_DIR/laravel_app/storage/framework/sessions"
mkdir -p "$DEPLOY_DIR/laravel_app/storage/framework/testing"
mkdir -p "$DEPLOY_DIR/laravel_app/storage/framework/views"
mkdir -p "$DEPLOY_DIR/laravel_app/storage/logs"

# Criar arquivos .gitkeep para manter as pastas
touch "$DEPLOY_DIR/laravel_app/storage/app/public/.gitkeep"
touch "$DEPLOY_DIR/laravel_app/storage/framework/cache/data/.gitkeep"
touch "$DEPLOY_DIR/laravel_app/storage/framework/sessions/.gitkeep"
touch "$DEPLOY_DIR/laravel_app/storage/framework/testing/.gitkeep"
touch "$DEPLOY_DIR/laravel_app/storage/framework/views/.gitkeep"
touch "$DEPLOY_DIR/laravel_app/storage/logs/.gitkeep"

# Definir permissões
log "Definindo permissões..."
chmod -R 755 "$DEPLOY_DIR/laravel_app/storage"
chmod -R 755 "$DEPLOY_DIR/laravel_app/bootstrap/cache"

# Criar arquivo de instruções
log "Criando arquivo de instruções..."
cat > "$DEPLOY_DIR/INSTRUCOES_DEPLOY.md" << 'EOF'
# 📋 INSTRUÇÕES DE DEPLOY - cPanel

## 🎯 Estrutura de Upload

### 1. Via FTP, faça upload dos arquivos:

```
📁 Seu cPanel
├── 📁 public_html/ (pasta pública do seu domínio)
│   └── [Conteúdo da pasta public_html/]
│
└── 📁 laravel_app/ (criar esta pasta FORA da public_html)
    └── [Conteúdo da pasta laravel_app/]
```

### 2. Configurações Importantes:

#### ⚙️ Arquivo .env (laravel_app/.env)
- ✅ Altere APP_URL para seu domínio
- ✅ Configure dados do banco MySQL do cPanel
- ✅ Gere nova APP_KEY: `php artisan key:generate`

#### 🗄️ Banco de Dados
1. Crie banco MySQL no cPanel
2. Importe estrutura (se houver migrations)
3. Configure credenciais no .env

#### 📁 Permissões (via File Manager do cPanel)
- storage/ → 755
- bootstrap/cache/ → 755

### 3. Teste Final:
- Acesse seu domínio
- Verifique se não há erros
- Teste todas as funcionalidades

## 🚨 Problemas Comuns:

### "500 Internal Server Error"
- Verifique permissões das pastas
- Confira logs de erro no cPanel
- Valide configurações do .env

### "APP_KEY não definida"
```bash
# Execute localmente e copie a chave:
php artisan key:generate --show
```

### Arquivos não encontrados
- Confirme estrutura de pastas
- Verifique se index.php está na public_html
- Confirme caminho para laravel_app

## 📞 Suporte:
Em caso de dúvidas, verifique os logs de erro do cPanel.
EOF

# Limpar cache local
log "Limpando cache local..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Reinstalar dependências de desenvolvimento
log "Reinstalando dependências de desenvolvimento..."
composer install

log "✅ Build concluído com sucesso!"
echo ""
echo -e "${BLUE}📁 Pasta de deploy criada:${NC} $DEPLOY_DIR"
echo -e "${BLUE}📋 Instruções:${NC} $DEPLOY_DIR/INSTRUCOES_DEPLOY.md"
echo ""
echo -e "${GREEN}🚀 Próximos passos:${NC}"
echo "1. Faça upload da pasta 'public_html' para a public_html do seu cPanel"
echo "2. Faça upload da pasta 'laravel_app' para fora da public_html"
echo "3. Configure o arquivo .env com dados do seu hosting"
echo "4. Teste o site!"