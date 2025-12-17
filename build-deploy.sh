#!/bin/bash

# Script de Deploy Otimizado para cPanel
# Autor: José Camejo (Revisado por Antigravity)
# Data: $(date +%Y-%m-%d)

# Parar o script se houver erro
set -e

# Configurações
DEPLOY_DIR="deploy-cpanel"
TEMP_BUILD_DIR=".temp_build_$(date +%s)"
TARGET_LARAVEL_PATH="/home2/josecamejocom/laravel_app" # Caminho no servidor (usado nas instruções)

# Cores
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m'

log() { echo -e "${GREEN}[$(date +'%H:%M:%S')]${NC} $1"; }
error() { echo -e "${RED}[ERROR]${NC} $1"; }
warning() { echo -e "${YELLOW}[WARNING]${NC} $1"; }

# 1. Verificações Iniciais
log "🚀 Iniciando processo de build seguro..."

if [ ! -f "artisan" ]; then
    error "Execute este script na raiz do projeto Laravel."
    exit 1
fi

# Limpar builds anteriores
if [ -d "$DEPLOY_DIR" ]; then
    warning "Removendo diretório de deploy anterior..."
    rm -rf "$DEPLOY_DIR"
fi
if [ -d "$TEMP_BUILD_DIR" ]; then
    rm -rf "$TEMP_BUILD_DIR"
fi

# 2. Preparar Ambiente de Build Temporário
# Copiamos TUDO para uma pasta temporária para não quebrar o ambiente de desenvolvimento local
log "📂 Criando ambiente de build isolado (copiando arquivos)..."
mkdir -p "$TEMP_BUILD_DIR"

# Rsync para copiar arquivos, excluindo o que não é necessário para o build
# Note que copiamos node_modules E vendor locais para agilizar, mas vamos limpar depois
rsync -a \
    --exclude='.git' \
    --exclude='.idea' \
    --exclude='.vscode' \
    --exclude="$DEPLOY_DIR" \
    --exclude="storage/logs/*" \
    --exclude="storage/framework/cache/*" \
    --exclude="storage/framework/sessions/*" \
    --exclude="storage/framework/views/*" \
    . "$TEMP_BUILD_DIR"

cd "$TEMP_BUILD_DIR"

# 3. Build do Frontend (Vite/NPM)
log "📦 Compilando assets (Frontend)..."
if [ -f "package.json" ]; then
    # Se não tiver node_modules copiado (ex: foi excluido), instala. 
    # Se já tiver, o comando é rápido.
    if [ ! -d "node_modules" ]; then
        npm ci --silent
    else
        # Apenas garante que está tudo certo
        npm install --silent
    fi
    
    npm run build
    
    # Remover node_modules do build final (não vai para o servidor PHP)
    rm -rf node_modules
else
    warning "package.json não encontrado. Pulei o build do frontend."
fi

# 4. Build do Backend (Composer)
log "🐘 Otimizando dependências PHP (Composer)..."
# Remove dev dependencies e otimiza autoloader
composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Limpar caches no build temporário (para não levar lixo local)
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Remover arquivos desnecessários para produção
rm -rf tests
rm -rf .env.example
rm -rf .gitattributes
rm -rf .gitignore
rm -rf README.md
rm -rf phpunit.xml

cd ..

# 5. Organizar Estrutura Final
log "🏗️  Montando estrutura para cPanel..."
mkdir -p "$DEPLOY_DIR/public_html"
mkdir -p "$DEPLOY_DIR/laravel_app"

# Mover conteúdo da pasta public do build para public_html final
mv "$TEMP_BUILD_DIR/public/"* "$DEPLOY_DIR/public_html/"
# Remover a pasta public vazia do build
rm -rf "$TEMP_BUILD_DIR/public"

# Mover o restante do app para laravel_app
mv "$TEMP_BUILD_DIR/"* "$DEPLOY_DIR/laravel_app/"
mv "$TEMP_BUILD_DIR/.[!.]"* "$DEPLOY_DIR/laravel_app/" 2>/dev/null || true # Copiar ocultos (.env se houver, etc)

# Remover pasta temporária
rm -rf "$TEMP_BUILD_DIR"

# 6. Configurar index.php
log "📝 Ajustando index.php..."
cat > "$DEPLOY_DIR/public_html/index.php" << 'EOF'
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Ajuste de caminho para manutençao
if (file_exists($maintenance = __DIR__.'/../laravel_app/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Carregar Autoloader
require __DIR__.'/../laravel_app/vendor/autoload.php';

// Inicializar App
$app = require_once __DIR__.'/../laravel_app/bootstrap/app.php';

$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

$kernel->terminate($request, $response);
EOF

# 7. Configuração .env
log "⚙️  Preparando .env..."
if [ -f ".env.production" ]; then
    cp .env.production "$DEPLOY_DIR/laravel_app/.env"
    log "✅ .env.production copiado como .env"
elif [ -f ".env" ]; then
    cp .env "$DEPLOY_DIR/laravel_app/.env.example_copy"
    warning "⚠️  Nenhum .env.production encontrado. Copiei seu .env local como .env.example_copy por segurança."
fi

# 8. Criar estrutura de storage limpa e arquivos .gitkeep
log "🗄️  Recriando estrutura de storage..."
STORAGE_DIRS=(
    "storage/app/public"
    "storage/framework/cache/data"
    "storage/framework/sessions"
    "storage/framework/testing"
    "storage/framework/views"
    "storage/logs"
)

for dir in "${STORAGE_DIRS[@]}"; do
    mkdir -p "$DEPLOY_DIR/laravel_app/$dir"
    touch "$DEPLOY_DIR/laravel_app/$dir/.gitkeep"
    chmod -R 755 "$DEPLOY_DIR/laravel_app/$(dirname "$dir")"
done

# Permissões mais abertas para pastas de escrita (o servidor vai ajustar user/group, mas o modo ajuda)
chmod -R 775 "$DEPLOY_DIR/laravel_app/storage"
chmod -R 775 "$DEPLOY_DIR/laravel_app/bootstrap/cache"

# 9. Instruções de Deploy
log "📄 Gerando manual de instruções..."
cat > "$DEPLOY_DIR/LEIA_ME.md" << EOF
# � Guia de Deploy - cPanel

Este pacote já está pronto para upload.

## 1. Estrutura de Pastas
O zip contém duas pastas principais:
- \`public_html/\`: Contém os arquivos públicos (index.php, css, js, images).
- \`laravel_app/\`: Contém o código fonte (backend, vendor, etc).

## 2. Instalação
1. Acesse o **Gerenciador de Arquivos** do cPanel.
2. Faça upload do conteúdo de \`public_html\` para a pasta \`public_html\` do seu domínio.
3. Crie uma pasta chamada \`laravel_app\` na RAIZ da sua conta (FORA da public_html).
   - Caminho esperado: \`/home/usuario/laravel_app\` ou \`$TARGET_LARAVEL_PATH\`
4. Faça upload do conteúdo de \`laravel_app\` para essa nova pasta.

## 3. Banco de Dados e Configuração
1. Edite o arquivo \`.env\` dentro da pasta \`laravel_app\` no servidor.
   - Ajuste \`DB_DATABASE\`, \`DB_USERNAME\`, \`DB_PASSWORD\`.
   - Ajuste \`APP_URL\` para seu domínio (ex: https://josecamejo.com.br).
   - Se necessário, gere uma nova key via SSH: \`php artisan key:generate\`.

## 4. Otimização (Via Terminal/SSH)
Recomendamos rodar estes comandos no servidor após o upload para garantir performance:

\`\`\`bash
cd $TARGET_LARAVEL_PATH
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link
\`\`\`

## 🚨 Solução de Problemas
- **Erro 500**: Verifique permissões das pastas \`storage\` e \`bootstrap/cache\` (devem ser 775 ou 755).
- **Tela Branca**: Verifique os logs em \`laravel_app/storage/logs/laravel.log\`.
EOF

# 10. Zipar para facilitar upload
log "🤐 Compactando pacote de deploy..."
if command -v zip &> /dev/null; then
    cd "$DEPLOY_DIR"
    zip -r "deploy_$(date +%Y%m%d).zip" . -x "*.DS_Store"
    log "✅ Arquivo 'deploy_$(date +%Y%m%d).zip' criado com sucesso!"
    cd ..
else
    warning "Comando 'zip' não encontrado. Os arquivos estão na pasta $DEPLOY_DIR."
fi

log "✅ PROCESSO CONCLUÍDO!"
echo ""
echo -e "${BLUE}📁  Arquivos prontos em:${NC} $(pwd)/$DEPLOY_DIR"
if [ -f "$DEPLOY_DIR/deploy_$(date +%Y%m%d).zip" ]; then
    echo -e "${BLUE}�  Pacote zip:${NC} $(pwd)/$DEPLOY_DIR/deploy_$(date +%Y%m%d).zip"
fi
echo ""