# Sistema de Vídeos do Blog

Este documento descreve o sistema completo de gerenciamento de vídeos implementado para o blog.

## Visão Geral

O sistema de vídeos permite o upload, gerenciamento e exibição de vídeos no painel administrativo, com funcionalidades similares ao sistema de imagens existente.

## Componentes Implementados

### 1. Modelo (BlogVideo)
- **Arquivo**: `app/Models/BlogVideo.php`
- **Funcionalidades**:
  - Geração automática de slug
  - Scopes para filtros (ativo, idioma)
  - Métodos para URLs de vídeo e thumbnail
  - Formatação de duração, tamanho de arquivo e dimensões
  - Soft deletes e timestamps

### 2. Migration
- **Arquivo**: `database/migrations/2025_09_28_131507_create_blog_videos_table.php`
- **Campos**:
  - `title`, `slug`, `description`
  - `filename`, `original_filename`, `path`
  - `mime_type`, `file_size`
  - `width`, `height`, `duration`
  - `thumbnail_path`
  - `language`, `is_active`, `sort_order`

### 3. Factory
- **Arquivo**: `database/factories/BlogVideoFactory.php`
- **Estados disponíveis**:
  - `active()` / `inactive()`
  - `avi()`, `short()`, `long()`

### 4. Controller
- **Arquivo**: `app/Http/Controllers/Admin/BlogVideoController.php`
- **Funcionalidades**:
  - CRUD completo
  - Upload de vídeos e thumbnails
  - Validação de arquivos
  - Filtros (status, idioma, busca)
  - Paginação

### 5. Request de Validação
- **Arquivo**: `app/Http/Requests/BlogVideoRequest.php`
- **Validações**:
  - Título e idioma obrigatórios
  - Vídeo: máx 500MB, formatos mp4/avi/mov/wmv
  - Thumbnail: máx 5MB, formatos jpg/jpeg/png/gif/webp

### 6. Views Administrativas

#### Index (`resources/views/admin/blog/videos/index.blade.php`)
- Lista de vídeos com paginação
- Filtros por status, idioma e busca
- Ações: visualizar, editar, excluir

#### Create (`resources/views/admin/blog/videos/create.blade.php`)
- Formulário de criação
- Upload com drag & drop
- Preview de vídeo e thumbnail

#### Edit (`resources/views/admin/blog/videos/edit.blade.php`)
- Formulário de edição
- Exibição do vídeo atual
- Upload opcional de novos arquivos

#### Show (`resources/views/admin/blog/videos/show.blade.php`)
- Visualização detalhada do vídeo
- Player de vídeo integrado
- Informações técnicas completas

### 7. Rotas
- **Arquivo**: `routes/admin.php`
- **Rota**: `Route::resource('blog-videos', BlogVideoController::class)`
- **Middleware**: `admin`

### 8. Configuração
- **Arquivo**: `config/blog.php`
- **Seção**: `videos`
- **Configurações**:
  - Storage (disco, caminhos, URLs)
  - Validação (tamanho máximo, tipos permitidos)
  - Thumbnails (geração automática, qualidade)
  - Processamento (otimização, metadados)

## Testes

### Testes Unitários (`tests/Unit/BlogVideoTest.php`)
- Criação de vídeos
- Geração de slugs
- Métodos de URL
- Scopes
- Formatação de dados

### Testes de Feature (`tests/Feature/BlogVideoControllerTest.php`)
- Acesso administrativo
- CRUD completo
- Validações
- Filtros e busca
- Upload de arquivos

## Funcionalidades Principais

### Upload de Vídeos
- Suporte a múltiplos formatos (MP4, AVI, MOV, WMV)
- Validação de tamanho (máx 500MB)
- Geração automática de nomes únicos
- Preservação do nome original

### Thumbnails
- Upload opcional de thumbnail personalizado
- Suporte a formatos de imagem (JPG, PNG, GIF, WebP)
- Validação de tamanho (máx 5MB)

### Interface Administrativa
- Design responsivo e moderno
- Drag & drop para uploads
- Preview de arquivos
- Filtros avançados
- Busca por título/descrição

### Validações e Segurança
- Validação rigorosa de tipos de arquivo
- Sanitização de nomes de arquivo
- Middleware de autenticação administrativa
- Proteção contra uploads maliciosos

## Estrutura de Arquivos

```
storage/app/public/blog/videos/
├── videos/          # Arquivos de vídeo
└── thumbnails/      # Thumbnails dos vídeos
```

## URLs Públicas

- Vídeos: `/storage/blog/videos/{filename}`
- Thumbnails: `/storage/blog/videos/thumbnails/{filename}`

## Comandos Úteis

```bash
# Executar testes específicos de vídeos
php artisan test --filter=BlogVideo

# Executar todos os testes
php artisan test

# Limpar cache de configuração
php artisan config:clear

# Criar link simbólico para storage
php artisan storage:link
```

## Próximos Passos Sugeridos

1. **API REST**: Implementar endpoints para consumo via API
2. **Processamento**: Adicionar conversão automática de formatos
3. **Streaming**: Implementar streaming adaptativo
4. **CDN**: Integração com serviços de CDN
5. **Analytics**: Tracking de visualizações e estatísticas

## Considerações de Performance

- Vídeos são armazenados localmente (considerar CDN para produção)
- Thumbnails são gerados sob demanda
- Implementar cache para metadados de vídeo
- Considerar compressão automática para vídeos grandes

## Manutenção

- Monitorar espaço em disco regularmente
- Implementar limpeza automática de arquivos órfãos
- Backup regular dos arquivos de vídeo
- Logs de upload e processamento