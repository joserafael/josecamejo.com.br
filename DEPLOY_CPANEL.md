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