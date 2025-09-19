# 🚀 Deploy Laravel para cPanel - Guia Completo

## 📋 Pré-requisitos

- ✅ Hosting com cPanel
- ✅ Acesso FTP (FileZilla, WinSCP, etc.)
- ✅ Banco MySQL disponível
- ✅ PHP 8.1+ no servidor

## 🛠️ Passo a Passo

### 1. 🏗️ Preparar Build Local

Execute o script de build:

```bash
./build-deploy.sh
```

Este script irá:
- ✅ Instalar dependências de produção
- ✅ Otimizar cache e rotas
- ✅ Criar estrutura para cPanel
- ✅ Gerar pasta `deploy-cpanel/`

### 2. 📁 Estrutura de Upload

Após executar o build, você terá:

```
deploy-cpanel/
├── 📁 public_html/     → Upload para public_html do cPanel
├── 📁 laravel_app/     → Upload para fora da public_html
└── 📄 INSTRUCOES_DEPLOY.md
```

### 3. 🌐 Upload via FTP

#### 3.1 Conectar ao FTP
- **Host:** ftp.seudominio.com.br
- **Usuário:** seu_usuario_cpanel
- **Senha:** sua_senha_cpanel
- **Porta:** 21 (ou 22 para SFTP)

#### 3.2 Upload dos Arquivos

**📂 Pasta public_html:**
```
Origem: deploy-cpanel/public_html/*
Destino: /public_html/
```

**📂 Pasta laravel_app:**
```
Origem: deploy-cpanel/laravel_app/
Destino: /laravel_app/ (criar fora da public_html)
```

### 4. ⚙️ Configuração no cPanel

#### 4.1 Banco de Dados MySQL
1. Acesse **MySQL Databases** no cPanel
2. Crie um novo banco: `seuusuario_portfolio`
3. Crie um usuário e associe ao banco
4. Anote: nome do banco, usuário e senha

#### 4.2 Configurar .env
Edite o arquivo `/laravel_app/.env`:

```env
APP_NAME="José Camejo - Portfolio"
APP_ENV=production
APP_KEY=base64:SUA_CHAVE_AQUI
APP_DEBUG=false
APP_URL=https://seudominio.com.br

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=seuusuario_portfolio
DB_USERNAME=seuusuario_dbuser
DB_PASSWORD=sua_senha_db
```

#### 4.3 Gerar APP_KEY
Execute localmente e copie a chave:
```bash
php artisan key:generate --show
```

#### 4.4 Permissões de Pastas
Via **File Manager** do cPanel, defina permissões:
- `laravel_app/storage/` → **755**
- `laravel_app/bootstrap/cache/` → **755**

### 5. 🧪 Teste Final

1. Acesse seu domínio
2. Verifique se o site carrega corretamente
3. Teste todas as funcionalidades
4. Monitore logs de erro no cPanel

## 🚨 Solução de Problemas

### ❌ "500 Internal Server Error"

**Possíveis causas:**
- Permissões incorretas
- Configuração .env inválida
- Caminho incorreto no index.php

**Soluções:**
1. Verifique permissões (755 para pastas, 644 para arquivos)
2. Valide configurações do .env
3. Confira logs de erro no cPanel

### ❌ "APP_KEY não definida"

```bash
# Execute localmente:
php artisan key:generate --show
# Copie a chave gerada para o .env
```

### ❌ "Failed to open stream: No such file or directory" (storage/framework/views)

**Causa:** Pastas de cache do Laravel não existem no servidor.

**🚀 Solução 1: Script Web (Recomendado)**
1. Faça upload do arquivo `fix-storage-prod.php` para a pasta `laravel_app/`
2. Acesse: `https://seudominio.com.br/fix-storage-prod.php`
3. Execute o script - ele fará diagnóstico completo e correções
4. **IMPORTANTE:** Delete o arquivo após usar!

**💻 Solução 2: Script CLI (via SSH)**
1. Faça upload do arquivo `fix-storage-cli.php` para a pasta `laravel_app/`
2. Acesse via SSH e execute: `php fix-storage-cli.php`
3. Siga as instruções na tela
4. Delete o arquivo após usar

**🛠️ Solução 3: Manual via cPanel**
```
Criar pastas em laravel_app/storage/:
├── app/public/
├── framework/cache/data/
├── framework/sessions/
├── framework/testing/
├── framework/views/
└── logs/

Permissões: 755 para todas as pastas
```

**🔍 Verificações Adicionais:**
- Confirme se o arquivo `.env` existe (renomeie `.env.production` se necessário)
- Verifique se as pastas têm permissões de leitura E escrita
- Teste se o PHP consegue criar arquivos nas pastas

**🚨 SOLUÇÃO EMERGENCIAL (RECOMENDADA)**

**⚡ Script de Correção para Produção cPanel**
**Use este script quando houver erro de caminhos misturados:**

1. **Upload**: Faça upload do arquivo `fix-production-paths.php` para a raiz do seu site
2. **Execute via SSH** (se tiver acesso):
   ```bash
   php fix-production-paths.php
   ```
3. **OU Execute via browser**: 
   - Acesse: `https://seudominio.com.br/fix-production-paths.php`
4. **Delete o arquivo** após usar:
   ```bash
   rm fix-production-paths.php
   ```

**✅ Este script:**
- **Detecta automaticamente** o ambiente cPanel
- **Corrige caminhos misturados** entre local e produção
- **Força caminhos corretos** do servidor (`/home2/josecamejocom/laravel_app/`)
- **Cria todas as pastas** necessárias com permissões corretas
- **Limpa cache** do Laravel automaticamente
- **Mostra debug detalhado** do que foi executado

## 🎨 Solução para Erro do Vite Manifest

Se você receber o erro: `Vite manifest not found at: /path/to/manifest.json`

### 🚀 Opção 1: Compilar Assets (Recomendado)

1. **No seu ambiente local:**
   ```bash
   npm install
   npm run build
   ```

2. **Fazer upload da pasta `public/build`** completa para o servidor

3. **Ou usar o script automático:**
   - Upload: `copy-vite-assets.php`
   - Execute: `php copy-vite-assets.php`
   - Delete após uso

### 📄 Opção 2: Manifest Básico (Emergencial)

Se não conseguir compilar o Vite:

- **Upload**: `create-basic-manifest.php`
- **Execute**: `php create-basic-manifest.php`
- **Delete**: Remova após uso

**Funcionalidades:**
- Cria manifest.json básico
- Gera CSS e JS essenciais
- Funciona sem Node.js/NPM
- Solução temporária para emergências

**🔧 Script Emergencial Genérico (Alternativo)**
Se o script acima não funcionar, use o `fix-emergency.php`:

1. **Upload**: `fix-emergency.php` para a raiz do site
2. **Execute**: Via SSH (`php fix-emergency.php`) ou browser
3. **Delete**: `rm fix-emergency.php`

---

**🚨 SEM ACESSO SSH/BROWSER? Use estas alternativas:**

**📍 Solução 4: Via Rota Temporária**
1. Copie o código do arquivo `temp-fix-route.php`
2. Cole no final do arquivo `routes/web.php`
3. Acesse: `https://seudominio.com.br/fix-storage-now`
4. **REMOVA** a rota após usar!

**📍 Solução 5: Via AppServiceProvider**
1. Copie o código do arquivo `fix-via-provider.php`
2. Cole no método `boot()` do `app/Providers/AppServiceProvider.php`
3. Acesse qualquer página do site
4. **REMOVA** o código após usar!

### ❌ Arquivos CSS/JS não carregam

1. Verifique se os arquivos estão na `public_html/`
2. Confirme permissões dos arquivos (644)
3. Teste URLs diretas dos assets

### ❌ Banco de dados não conecta

1. Verifique credenciais no .env
2. Confirme se o usuário tem permissões no banco
3. Teste conexão via phpMyAdmin

## 📊 Monitoramento

### Logs de Erro
- **cPanel:** Error Logs
- **Laravel:** `laravel_app/storage/logs/`

### Performance
- Use **GTmetrix** ou **PageSpeed Insights**
- Monitore uso de recursos no cPanel

## 🔄 Atualizações Futuras

Para atualizar o site:

1. Execute `./build-deploy.sh` localmente
2. Faça backup do `.env` atual
3. Upload apenas dos arquivos alterados
4. Restaure configurações do `.env`

## 📞 Suporte

### Comandos Úteis (local)

```bash
# Limpar cache
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recriar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar configuração
php artisan about
```

### Checklist de Deploy

- [ ] Build executado com sucesso
- [ ] Arquivos enviados via FTP
- [ ] Banco de dados criado e configurado
- [ ] .env configurado corretamente
- [ ] APP_KEY gerada
- [ ] Permissões definidas
- [ ] Site testado e funcionando
- [ ] Logs verificados

---

**💡 Dica:** Mantenha sempre um backup dos arquivos antes de fazer atualizações!

**🎯 Resultado:** Seu portfolio Laravel estará rodando perfeitamente no cPanel!