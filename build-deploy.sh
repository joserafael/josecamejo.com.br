#!/bin/bash

# Script de Deploy Otimizado para cPanel v2
# Autor: José Camejo (Revisado por Antigravity)
# Data: $(date +%Y-%m-%d)
#
# v2 Changelog:
# - Força a reinstalação limpa do 'vendor' para garantir integridade.
# - Remove arquivos de documentação do build final.
# - Enfatiza o uso do ZIP para evitar erros de upload.

# Parar o script se houver erro
set -e

# Configurações
DEPLOY_DIR="deploy-cpanel"
TEMP_BUILD_DIR=".temp_build_$(date +%s)"
TARGET_LARAVEL_PATH="/home2/josecamejocom/laravel_app"

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
log "🚀 Iniciando processo de build (v2 - Instalação Limpa)..."

if [ ! -f "artisan" ]; then
    error "Execute este script na raiz do projeto Laravel."
    exit 1
fi

# Limpar builds anteriores
if [ -d "$DEPLOY_DIR" ]; then
    rm -rf "$DEPLOY_DIR"
fi
if [ -d "$TEMP_BUILD_DIR" ]; then
    rm -rf "$TEMP_BUILD_DIR"
fi

# 2. Preparar Ambiente de Build Temporário
log "📂 Criando ambiente de build isolado..."
mkdir -p "$TEMP_BUILD_DIR"

# Rsync para copiar arquivos
# Excluindo pastas pesadas locais para não copiar lixo e depois deletar
rsync -a \
    --exclude='.git' \
    --exclude='.idea' \
    --exclude='.vscode' \
    --exclude="$DEPLOY_DIR" \
    --exclude="node_modules" \
    --exclude="storage/logs/*" \
    --exclude="storage/framework/cache/*" \
    --exclude="storage/framework/sessions/*" \
    --exclude="storage/framework/views/*" \
    --exclude="DEPLOY_CPANEL.md" \
    --exclude="WARP.md" \
    --exclude="BLOG_VIDEOS_SYSTEM.md" \
    . "$TEMP_BUILD_DIR"

cd "$TEMP_BUILD_DIR"

# 3. Build do Frontend (Vite/NPM)
log "📦 Compilando assets (Frontend)..."
if [ -f "package.json" ]; then
    # Instalar deps do node apenas se necessário para build
    log "   Instalando dependências Node..."
    npm ci --silent
    log "   Rodando npm run build..."
    npm run build
    
    # Remover node_modules do build final
    rm -rf node_modules
else
    warning "package.json não encontrado. Pulei o build do frontend."
fi

# 4. Build do Backend (Composer) - MODO LIMPO
log "🐘 Instalando dependências PHP (Composer CLEAN)..."

# Garantir que a pasta vendor antiga não existe (embora rsync sem --delete preserve, aqui já não copiamos se não excluímos no rsync acima... 
# Espere, no rsync acima removi '--exclude=vendor' que existia na minha mente? 
# Ops, o rsync acima COPIOU o vendor local se eu não excluí.
# Vamos deletar explicitamente para garantir que o Composer baixe tudo fresco.
if [ -d "vendor" ]; then
    log "   Removendo vendor local copiado para garantir integridade..."
    rm -rf vendor
fi

# Instalação limpa
composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Limpar caches no build temporário
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Remover arquivos inúteis em produção
rm -rf tests
rm -rf .env.example
rm -rf .gitattributes
rm -rf .gitignore
rm -rf README.md
rm -rf phpunit.xml
rm -rf build-deploy.sh

cd ..

# 5. Organizar Estrutura Final
log "🏗️  Montando estrutura para cPanel..."
mkdir -p "$DEPLOY_DIR/public_html"
mkdir -p "$DEPLOY_DIR/laravel_app"

# Mover conteúdo da pasta public do build para public_html final
if [ -d "$TEMP_BUILD_DIR/public" ]; then
    mv "$TEMP_BUILD_DIR/public/"* "$DEPLOY_DIR/public_html/"
    rm -rf "$TEMP_BUILD_DIR/public"
fi

# Mover o restante do app para laravel_app
mv "$TEMP_BUILD_DIR/"* "$DEPLOY_DIR/laravel_app/"
mv "$TEMP_BUILD_DIR/.[!.]"* "$DEPLOY_DIR/laravel_app/" 2>/dev/null || true

# Remover pasta temporária
rm -rf "$TEMP_BUILD_DIR"

# 6. Configurar index.php
log "📝 Ajustando index.php..."
cat > "$DEPLOY_DIR/public_html/index.php" << 'EOF'
<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

if (file_exists($maintenance = __DIR__.'/../laravel_app/storage/framework/maintenance.php')) {
    require $maintenance;
}

require __DIR__.'/../laravel_app/vendor/autoload.php';

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
elif [ -f ".env" ]; then
    cp .env "$DEPLOY_DIR/laravel_app/.env"
fi

# 8. Criação de Storage e Permissões
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
    chmod -R 775 "$DEPLOY_DIR/laravel_app/$(dirname "$dir")"
done

chmod -R 775 "$DEPLOY_DIR/laravel_app/bootstrap/cache"

# 9. INSTRUÇÕES CRÍTICAS
log "📄 Gerando instruções..."
cat > "$DEPLOY_DIR/LEIA_ANTES_DE_ENVIAR.txt" << EOF
!!! ATENÇÃO !!!

A CAUSA MAIS COMUM DE ERROS É A FALTA DE ARQUIVOS NO UPLOAD.
A pasta 'vendor' contém milhares de arquivos pequenos.
Enviar por FTP comum quase sempre falha em alguns arquivos, causando o erro:
"Failed opening required..."

SOLUÇÃO OBRIGATÓRIA:
1. USE O ARQUIVO ZIP GERADO! ('deploy_cpanel_v2.zip')
2. Envie o ZIP para o cPanel via Gerenciador de Arquivos.
3. Clique em "Extrair" (Extract) dentro do cPanel.

NÃO ENVIE AS PASTAS SOLTAS POR FTP, a menos que tenha certeza absoluta.

Passos Pós-Upload:
1. Coloque o conteúdo de 'public_html' na pasta 'public_html' do domínio.
2. Coloque a pasta 'laravel_app' na raiz (fora da public_html).
3. Ajuste o .env com a senha do banco.
4. Rode no terminal do cPanel (opcional, mas recomendado):
   cd ~/laravel_app
   php artisan config:cache
   php artisan route:cache

EOF

# 10. Zipar
log "🤐 Compactando pacote de deploy..."
ZIP_NAME="deploy_cpanel_$(date +%Y%m%d).zip"
if command -v zip &> /dev/null; then
    cd "$DEPLOY_DIR"
    zip -q -r "$ZIP_NAME" . -x "*.DS_Store"
    log "✅ Arquivo '$ZIP_NAME' criado com sucesso!"
    cd ..
else
    warning "Zip não encontrado. Use os arquivos da pasta $DEPLOY_DIR"
fi

echo ""
echo -e "${GREEN}✅ BUILD V2 FINALIZADO!${NC}"
echo -e "${BLUE}📁 Local:${NC} $(pwd)/$DEPLOY_DIR"
echo -e "${RED}⚠️  IMPORTANTE: Utilize o arquivo ZIP ($ZIP_NAME) para fazer o upload!${NC}"
echo -e "   Enviar arquivos soltos por FTP é a causa #1 de erros (arquivos faltando)."
echo ""